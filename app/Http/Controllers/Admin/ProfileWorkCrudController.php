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
        CRUD::setFromDb(); // columns

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
            'options' => ['personnel' => 'Personnel', 'manager' => 'Manager','ceo'=>'CEO'],
            'allows_null' => false,
            'default' => 'one',
        ]);

        $this->crud->addField([
            'name'=>'phone',
            'type'=>'text',
            'label'=>'Phone',
        ]);
        $this->crud->addField([
            'name'=>'address',
            'type'=>'text',
            'label'=>'Address',
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
        $this->setupCreateOperation();
    }
}
