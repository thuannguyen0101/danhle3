<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\TeamRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class TeamCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class TeamCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Team::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/team');
        CRUD::setEntityNameStrings('team', 'teams');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
//        CRUD::setFromDb(); // columns


        $this->crud->addColumn([
            'label' => 'Department', // Table column heading
            'type' => 'select',
            'name' => 'department_id', // the column that contains the ID of that connected entity;
            'entity' => 'department', // the method that defines the relationship in your Model
            'attribute' => 'name', // foreign key attribute that is shown to user
            'model' => "App\Models\Department", // foreign key model
        ]);

        $this->crud->addColumn([
            'label' => 'Leader', // Table column heading
            'type' => 'select',
            'name' => 'leader_id', // the column that contains the ID of that connected entity;
            'entity' => 'leader', // the method that defines the relationship in your Model
            'attribute' => 'name', // foreign key attribute that is shown to user
            'model' => "App\Models\User", // foreign key model
        ]);
        $this->crud->addColumn([
            'name' => 'name', // the db column name (attribute name)
            'label' => "Name", // the human-readable label for it
            'type' => 'text' // the kind of column to show
        ]);
        $this->crud->addColumn([
            'name' => 'description', // the db column name (attribute name)
            'label' => "Description", // the human-readable label for it
            'type' => 'text' // the kind of column to show
        ]);

        $this->crud->addColumn([
            'name' => 'status', // the db column name (attribute name)
            'label' => "Status", // the human-readable label for it
            'type' => 'boolean', // the kind of column to show
            'options' => [0 => 'Inactive', 1 => 'Active']
        ]);


        $this->crud->addFilter([
            'type' => 'text',
            'name' => 'description',
            'label' => 'Description'
        ],
            false,
            function ($value) { // if the filter is active
                $this->crud->addClause('where', 'description', 'LIKE', "%$value%");
        });

        $this->crud->addFilter([
            'name' => 'name',
            'type' => 'dropdown',
            'label' => 'Name Department'
        ],function() {
            return \App\Models\Department::all()->pluck('name', 'id')->toArray();
        }, function ($value) { //
            $this->crud->addClause('where', 'department_id', $value);
        });

        $this->crud->addFilter([
            'type' => 'text',
            'name' => 'leader',
            'label' => 'Search by name Leader'
        ],
            false,
            function ($value) {
                $this->crud->addClause('whereHas','leader',function($query) use ($value){
                    $query->where('name','like', '%'.$value.'%');
                } );
            }
        );



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
        CRUD::setValidation(TeamRequest::class);

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
