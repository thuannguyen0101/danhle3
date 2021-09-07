<?php
namespace App\Http\Controllers\Admin;
use App\Http\Requests\TeamDetailRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
/**
 * Class TeamDetailCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class TeamDetailCrudController extends CrudController
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
        CRUD::setModel(\App\Models\TeamDetail::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/team-detail');
        CRUD::setEntityNameStrings('team detail', 'team details');
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
        $this->crud->removeAllButtons();

//        CRUD::setFromDb(); // columns

        $this->crud->addColumn([
            'label'     => 'Team', // Table column heading
            'type'      => 'select',
            'name'      => 'team_id', // the column that contains the ID of that connected entity;
            'entity'    => 'team', // the method that defines the relationship in your Model
            'attribute' => 'name', // foreign key attribute that is shown to user
            'model'     => "App\Models\Team", // foreign key model
        ]);

        $this->crud->addColumn([
            'label'     => 'Employee', // Table column heading
            'type'      => 'select',
            'name'      => 'employee_id', // the column that contains the ID of that connected entity;
            'entity'    => 'user', // the method that defines the relationship in your Model
            'attribute' => 'name', // foreign key attribute that is shown to user
            'model'     => "App\Models\User", // foreign key model
        ]);

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']);
         */

        $this->crud->addFilter([
            'type' => 'text',
            'name' => 'team',
            'label' => 'Search by team name'
        ],
            false,
            function ($value) {
                $this->crud->addClause('whereHas','team',function($query) use ($value){
                    $query->where('name','like', '%'.$value.'%');
                } );
            }
        );
        $this->crud->addFilter([
            'type' => 'text',
            'name' => 'member',
            'label' => 'Search by team member name'
        ],
            false,
            function ($value) {
                $this->crud->addClause('whereHas','user',function($query) use ($value){
                    $query->where('name','like', '%'.$value.'%');
                } );
            }
        );

        $this->crud->addFilter([
            'name' => 'select_team',
            'type' => 'dropdown',
            'label' => 'Team'
        ],function() {
            return \App\Models\Team::all()->pluck('name', 'id')->toArray();
        }, function ($value) { //
            $this->crud->addClause('where', 'team_id', $value);
        });
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(TeamDetailRequest::class);

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
