<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\TimekeepingRequest;
use App\Models\Timekeeping;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Carbon\Carbon;

/**
 * Class TimekeepingCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class TimekeepingCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation {
        update as traitStore;
    }

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(Timekeeping::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/timekeeping');
        CRUD::setEntityNameStrings('timekeeping', 'timekeeping');
    }

    public function update()
    {
        $timeKeeping = Timekeeping::find($this->crud->getRequest()->id);
        $startTime = $this->crud->getRequest()->start_time;
        $end_time = $this->crud->getRequest()->end_time;
        $timeKeeping->update([
            'end_time' => $end_time,
            'total_time' => floatval(date('H.i', strtotime($end_time))) - floatval(date('H.i', strtotime($startTime))) - 1,
            'late_attendance'=>0
        ]);
        $timeKeeping->save();

        return redirect()->route('timekeeping.index');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        $this->crud->removeButton('create');
        $this->crud->removeButton('show');
        $this->crud->removeButton('delete');

        CRUD::addColumn([
            'label' => "Tên Nhân Viên",
            'type' => 'select',
            'name' => 'user_id',
            'entity' => 'user',
            'model' => "App\Models\User",
            'attribute' => 'name',
        ]);

        CRUD::addColumn([
            'label' => "Giờ bắt đầu làm việc",
            'type' => 'dateTime',
            'name' => 'start_time',
        ]);

        CRUD::addColumn([
            'label' => "Giờ nghỉ",
            'type' => 'dateTime',
            'name' => 'end_time',
        ]);

        CRUD::addColumn([
            'label' => "Tổng thời gian làm việc",
            'type' => 'integer',
            'name' => 'total_time',
        ]);

        CRUD::addColumn([
            'name' => 'late_start',
            'label' => "Đi muộn",
            'type' => 'boolean',
            'options' => [1 => 'Không', 0 => 'Đã đi muộn']
        ]);

        CRUD::addColumn([
            'name' => 'late_attendance',
            'label' => "Chấm công muộn",
            'type' => 'boolean',
            'options' => [1 => 'Không', 0 => 'Muộn']
        ]);

        CRUD::addFilter([
            'type' => 'date',
            'name' => 'date',
            'label' => 'Tìm theo ngày làm việc'
        ], false,
            function ($value) {
                $this->crud->addClause('where', 'start_time', 'like', '%' . $value . '%');
            });

        $this->crud->addFilter([
            'name' => 'total_time',
            'type' => 'dropdown',
            'label' => 'Lọc theo giờ làm'
        ], [
            1 => 'Làm đủ giờ',
            2 => 'Làm thiếu giờ',
        ], function ($value) {
            if ($value == 1) {
                $this->crud->addClause('where', 'total_time', '>=', 8);
            } else {
                $this->crud->addClause('where', 'total_time', '<', 8);
            }
        });

        $this->crud->addFilter([
            'name' => 'late_start',
            'type' => 'dropdown',
            'label' => 'Lọc theo trạng thái đi muộn'
        ], [
            0 => 'Đi muộn',
            1 => 'Không đi muộn',
        ], function ($value) {
            if ($value == 1) {
                $this->crud->addClause('where', 'late_start', '=', $value);
            } else {
                $this->crud->addClause('where', 'late_start', '=', $value);
            }
        });

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']);
         */
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(TimekeepingRequest::class);

        CRUD::setFromDb(); // fields

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number']));
         */
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();

        $this->crud->removeAllFields();

        $this->crud->addFields([
            [
                'name' => 'user.name',
                'type' => 'text',
                'label' => 'User',
                'attributes' => [
                    'disabled' => true
                ],
            ],

            [
                'label' => "Giờ bắt đầu",
                'name' => 'start_time',
                'wrapper' => [
                    'class' => 'form-group col-md-6 col-sm-12'
                ],
            ],

            [
                'label' => "Giờ nghỉ",
                'name' => 'end_time',
                'wrapper' => [
                    'class' => 'form-group col-md-6 col-sm-12'
                ],
            ],
        ]);
    }
}
