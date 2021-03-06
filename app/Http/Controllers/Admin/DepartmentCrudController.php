<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\DepartmentRequest;
use App\Models\Department;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class DepartmentCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class DepartmentCrudController extends CrudController
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
        CRUD::setModel(Department::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/department');
        CRUD::setEntityNameStrings('department', 'departments');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        $this->crud->addFilter([
            'type' => 'text',
            'name' => 'manager',
            'label' => 'Tìm theo tền trưởng phòng'
        ], false,
            function ($value) {
                $this->crud->addClause('whereHas','manager',function($query) use ($value){
                    $query->where('name','like', '%'.$value.'%');
                } );
            }
        );

        $this->crud->addFilter([
            'name' => 'name',
            'type' => 'dropdown',
            'label' => 'Tìm theo tên phòng'
        ],function() {
            return Department::all()->pluck('name', 'name')->toArray();
        }, function ($value) {
            $this->crud->addClause('where', 'name', $value);
        });

        CRUD::addColumn([
            'label' => "Trường phòng",
            'type' => 'select',
            'name' => 'manager_id',
            'entity' => 'manager',
            'model' => "App\Models\User",
            'attribute' => 'name',
        ]);

        CRUD::addColumn([
            'label' => "Tên phòng",
            'type' => 'text',
            'name' => 'name',
        ]);

        CRUD::addColumn([
            'label' => "Mô tả",
            'type' => 'text',
            'name' => 'description',
        ]);
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(DepartmentRequest::class);

        CRUD::addField([
            'label' => "Trường Phòng",
            'type' => 'select',
            'name' => 'manager_id',
            'entity' => 'manager',
            'model' => "App\Models\User",
            'attribute' => 'name',
            'options' => (function ($query) {
                return $query->orderBy('name', 'ASC')->get();
            }),
            'wrapper'   => [
                'class'      => 'form-group col-md-6'
            ],
        ]);

        CRUD::addField([
            'name'  => 'name',
            'type'  => 'text',
            'label' => 'Tên phòng',
            'wrapper'   => [
                'class'      => 'form-group col-md-6'
            ],
        ]);

        CRUD::addField([
            'name'  => 'description',
            'type'  => 'textarea',
            'label' => 'Mô tả phòng ban',
            'attributes' => [
                'rows' => 5,
            ]
        ]);
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
