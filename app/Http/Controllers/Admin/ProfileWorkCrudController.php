<?php

namespace App\Http\Controllers\Admin;
use App\Http\Requests\ProfileWorkRequest;
use App\Models\ProfileWork;
use App\Models\TeamDetail;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;


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
            'label' => 'Tìm theo tên nhân viên'
        ], false,
            function ($value) {
                $this->crud->addClause('whereHas', 'user', function ($query) use ($value) {
                    $query->where('name', 'like', '%' . $value . '%');
                });
            }
        );

        $this->crud->addFilter([
            'name' => 'name',
            'type' => 'dropdown',
            'label' => 'Tìm theo phòng'
        ], function () {
            return \App\Models\Department::all()->pluck('name', 'id')->toArray();
        }, function ($value) {
            $this->crud->addClause('where', 'department_id', $value);
        });

        $this->crud->addFilter([
            'name' => 'team',
            'type' => 'dropdown',
            'label' => 'Tìm theo nhóm'
        ], function () {
            return \App\Models\Team::all()->pluck('name', 'id')->toArray();
        }, function ($value) {
            $this->crud->addClause('whereHas','teamDetail',function($query) use ($value){
                $query->where('team_id', $value);
            } );
        });

        CRUD::addColumn([
            'label' => "Tên Nhân Viên",
            'type' => 'select',
            'name' => 'employee_id',
            'entity' => 'user',
            'model' => "App\Models\User",
            'attribute' => 'name',
        ]);

        CRUD::addColumn([
            'label' => "Phòng",
            'type' => 'select',
            'name' => 'department_id',
            'entity' => 'department',
            'model' => "App\Models\Department",
            'attribute' => 'name',
        ]);

        CRUD::addColumn([
            'label' => "Chức Vụ",
            'type' => 'text',
            'name' => 'position',
        ]);

        CRUD::addColumn([
            'label' => "Nhóm",
            'type' => 'text',
            'name' => 'teamDetail.team.name',
        ]);

        CRUD::addColumn([
            'label' => "Số Điện Thoại",
            'type' => 'text',
            'name' => 'phone',
        ]);

        CRUD::addColumn([
            'label' => "Địa Chỉ",
            'type' => 'text',
            'name' => 'address',
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
        CRUD::setValidation(ProfileWorkRequest::class);

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
            'entity'=> 'teams',
            'model' => "App\Models\Team",
            'attribute' => 'name',
            'options' => (function ($query) {
                return $query->orderBy('name', 'ASC')->get();
            }),
        ]);

        $this->crud->addField([
            'name' => 'work_location',
            'label' => "Work Location",
            'type' => 'select_from_array',
            'options' => ['21.031, 105.783' => 'Hà Nội', '35.682, 139.772' => 'JP', '21.031, 105.785' => 'HCM'],
            'allows_null' => false,
            'default' => 'one',
        ]);
        $this->crud->addField([
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

        $this->crud->addField([
            'name' => 'work_location',
            'label' => "Work Location",
            'type' => 'select_from_array',
            'options' => ['21.031, 105.783' => 'Hà Nội', '35.682, 139.772' => 'JP', '21.031, 105.785' => 'HCM'],
            'allows_null' => false,
            'default' => 'one',
        ]);
        $this->crud->addField([
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
