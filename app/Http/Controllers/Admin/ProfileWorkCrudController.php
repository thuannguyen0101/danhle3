<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ProfileWorkRequest;
use App\Models\ProfileWork;
use App\Models\Team;
use App\Models\TeamDetail;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ProfileWorkCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ProfileWorkCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation {
        store as traitStore;
    }
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation {
        update as traitUpdate;
    }

    public function store()
    {
        $profile_work = new ProfileWork();
        $profile_work->department_id = $this->crud->getRequest()->department_id;
        $profile_work->phone = $this->crud->getRequest()->phone;
        $profile_work->address = $this->crud->getRequest()->address;
        $profile_work->work_location = $this->crud->getRequest()->work_location;
        $profile_work->position = $this->crud->getRequest()->position;
        $profile_work->employee_id = backpack_user()->id;
        $profile_work->save();
        $team_detail = new TeamDetail();
        $team_detail->team_id = $this->crud->getRequest()->team_id;
        $team_detail->employee_id = backpack_user()->id;
        $team_detail->save();
        return redirect()->route('profile-work.index');
    }

    public function update()
    {
        $profile_work = ProfileWork::find($this->crud->getRequest()->id);
        $team_detail = TeamDetail::where('employee_id',$profile_work->employee_id)->first();
        $profile_work->update($this->crud->getRequest()->all());
        $profile_work->save();

        $team_detail->update([
            'team_id'=>$this->crud->getRequest()->team_id
        ]);
        $team_detail->save();
        return redirect()->route('profile-work.index');
    }


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
        CRUD::setModel(\App\Models\ProfileWork::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/profile-work');
        CRUD::setEntityNameStrings('profile work', 'profile works');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        $this->crud->addClause('with','teamDetail.team');
        $this->crud->addFilter([
            'type' => 'text',
            'name' => 'employee',
            'label' => 'Search by name Employee'
        ],
            false,
            function ($value) {
                $this->crud->addClause('whereHas', 'employee', function ($query) use ($value) {
                    $query->where('name', 'like', '%' . $value . '%');
                });
            }
        );
        $this->crud->addFilter([
            'name' => 'name',
            'type' => 'dropdown',
            'label' => 'Name Department'
        ], function () {
            return \App\Models\Department::all()->pluck('name', 'id')->toArray();
        }, function ($value) {
            $this->crud->addClause('where', 'department_id', $value);
        });
        $this->crud->addFilter([
            'name' => 'team',
            'type' => 'dropdown',
            'label' => 'Team'
        ], function () {
            return \App\Models\Team::all()->pluck('name', 'id')->toArray();
        }, function ($value) {
            $this->crud->addClause('whereHas','teamDetail',function($query) use ($value){
                $query->where('team_id', $value);
            } );
        });
        CRUD::addColumn([
            'label' => "Employee",
            'type' => 'select',
            'name' => 'employee_id',
            'entity' => 'employee',
            'model' => "App\Models\User",
            'attribute' => 'name',
        ]);
        CRUD::addColumn([
            'label' => "Department",
            'type' => 'select',
            'name' => 'department_id',
            'entity' => 'department',
            'model' => "App\Models\Department",
            'attribute' => 'name',
        ]);

        CRUD::addColumn([
            'label' => "Position",
            'type' => 'text',
            'name' => 'position',
        ]);
        CRUD::addColumn([
            'label' => "Team",
            'type' => 'text',
            'name' => 'teamDetail.team.name',
        ]);
        CRUD::addColumn([
            'label' => "Phone",
            'type' => 'text',
            'name' => 'phone',
        ]);
        CRUD::addColumn([
            'label' => "Address",
            'type' => 'text',
            'name' => 'address',
        ]);
        CRUD::addColumn([
            'label' => "Work location",
            'type' => 'text',
            'name' => 'work_location',
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
        CRUD::setValidation(ProfileWorkRequest::class);

//        CRUD::setFromDb(); // fields
        $this->crud->addField([
            'label' => "Department",
            'type' => 'select',
            'name' => 'department_id',
            'model' => "App\Models\Department",
            'attribute' => 'name',
            'options' => (function ($query) {
                return $query->orderBy('name', 'ASC')->get();
            }),
        ]);

        $this->crud->addField([
            'label' => "Team",
            'type' => 'select',
            'name' => 'team_id',
            'model' => "App\Models\Team",
            'attribute' => 'name',
            'options' => (function ($query) {
                return $query->orderBy('name', 'ASC')->get();
            }),
        ]);

        $this->crud->addField([   // select_from_array
            'name' => 'work_location',
            'label' => "Work Location",
            'type' => 'select_from_array',
            'options' => ['21.031, 105.783' => 'Hà Nội', '35.682, 139.772' => 'JP', '21.031, 105.785' => 'HCM'],
            'allows_null' => false,
            'default' => 'one',
        ]);
        $this->crud->addField([   // select_from_array
            'name' => 'position',
            'label' => "Position",
            'type' => 'select_from_array',
            'options' => ['personnel' => 'Personnel', 'manager' => 'Manager', 'ceo' => 'CEO'],
            'allows_null' => false,
            'default' => 'one',
        ]);

        $this->crud->addField([
            'name' => 'phone',
            'type' => 'text',
            'label' => 'Phone',
        ]);
        $this->crud->addField([
            'name' => 'address',
            'type' => 'text',
            'label' => 'Address',
        ]);
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
        $profile_work = ProfileWork::find($this->crud->getRequest()->id);
        $team_detail = TeamDetail::where('employee_id',$profile_work->employee_id)->get();
        $key = $team_detail[0]->team_id;
        $this->crud->addField([
            'label' => "Department",
            'type' => 'select',
            'name' => 'department_id',
            'model' => "App\Models\Department",
            'attribute' => 'name',
            'options' => (function ($query) {
                return $query->orderBy('name', 'ASC')->get();
            }),
        ]);

        $this->crud->addField([
            'label' => "Team",
            'type' => 'select',
            'name' => 'team_id',
            'model' => "App\Models\Team",
            'attribute' => 'name',
            'options' => (function ($query) {
                return $query->orderBy('name', 'ASC')->get();
            }),
            'default' => "$key",
        ]);

        $this->crud->addField([   // select_from_array
            'name' => 'work_location',
            'label' => "Work Location",
            'type' => 'select_from_array',
            'options' => ['21.031, 105.783' => 'Hà Nội', '35.682, 139.772' => 'JP', '21.031, 105.785' => 'HCM'],
            'allows_null' => false,
            'default' => 'one',
        ]);
        $this->crud->addField([   // select_from_array
            'name' => 'position',
            'label' => "Position",
            'type' => 'select_from_array',
            'options' => ['personnel' => 'Personnel', 'manager' => 'Manager', 'ceo' => 'CEO'],
            'allows_null' => false,
            'default' => 'one',
        ]);

        $this->crud->addField([
            'name' => 'phone',
            'type' => 'text',
            'label' => 'Phone',
        ]);
        $this->crud->addField([
            'name' => 'address',
            'type' => 'text',
            'label' => 'Address',
        ]);
    }
}
