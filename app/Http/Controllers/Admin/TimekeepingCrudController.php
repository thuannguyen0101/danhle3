<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\TimekeepingRequest;
use App\Models\Timekeeping;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

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
    }
}
