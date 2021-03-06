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
            'label' => "T??n Nh??n Vi??n",
            'type' => 'select',
            'name' => 'user_id',
            'entity' => 'user',
            'model' => "App\Models\User",
            'attribute' => 'name',
        ]);

        CRUD::addColumn([
            'label' => "Gi??? b???t ?????u l??m vi???c",
            'type' => 'dateTime',
            'name' => 'start_time',
        ]);

        CRUD::addColumn([
            'label' => "Gi??? ngh???",
            'type' => 'dateTime',
            'name' => 'end_time',
        ]);

        CRUD::addColumn([
            'label' => "T???ng th???i gian l??m vi???c",
            'type' => 'integer',
            'name' => 'total_time',
        ]);

        CRUD::addColumn([
            'name' => 'late_start',
            'label' => "??i mu???n",
            'type' => 'boolean',
            'options' => [1 => 'Kh??ng', 0 => '???? ??i mu???n']
        ]);

        CRUD::addColumn([
            'name' => 'late_attendance',
            'label' => "Ch???m c??ng mu???n",
            'type' => 'boolean',
            'options' => [1 => 'Kh??ng', 0 => 'Mu???n']
        ]);

        CRUD::addFilter([
            'type' => 'date',
            'name' => 'date',
            'label' => 'T??m theo ng??y l??m vi???c'
        ], false,
            function ($value) {
                $this->crud->addClause('where', 'start_time', 'like', '%' . $value . '%');
            });

        $this->crud->addFilter([
            'name' => 'total_time',
            'type' => 'dropdown',
            'label' => 'L???c theo gi??? l??m'
        ], [
            1 => 'L??m ????? gi???',
            2 => 'L??m thi???u gi???',
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
            'label' => 'L???c theo tr???ng th??i ??i mu???n'
        ], [
            0 => '??i mu???n',
            1 => 'Kh??ng ??i mu???n',
        ], function ($value) {
            if ($value == 1) {
                $this->crud->addClause('where', 'late_start', '=', $value);
            } else {
                $this->crud->addClause('where', 'late_start', '=', $value);
            }
        });

        $this->crud->addFilter([
            'name' => 'end_time',
            'type' => 'dropdown',
            'label' => 'Qu??n ch???m c??ng'
        ], [
            0 => 'Qu??n ch???m c??ng',
            1 => '???? ch???m c??ng',
        ], function ($value) {
            if ($value == 1) {
                $this->crud->addClause('where', 'end_time', '!=', null);
            } else {
                $this->crud->addClause('where', 'end_time', '=', null);
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
                'label' => "Gi??? b???t ?????u",
                'name' => 'start_time',
                'wrapper' => [
                    'class' => 'form-group col-md-6 col-sm-12'
                ],
            ],

            [
                'label' => "Gi??? ngh???",
                'name' => 'end_time',
                'wrapper' => [
                    'class' => 'form-group col-md-6 col-sm-12'
                ],
            ],
        ]);
    }
}
