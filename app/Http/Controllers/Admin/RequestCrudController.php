<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\RequestRequest;
use App\Models\Request;
use App\Models\TeamDetail;
use App\Models\User;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Mail;

/**
 * Class RequestCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class RequestCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation {
        store as traitStore;
    }

    public function store()
    {
        $request = new Request();
        $request->sender_id = backpack_user()->id;
        $request->request_type = $this->crud->getRequest()->request_type;
        $request->message = $this->crud->getRequest()->message;
        $request->state = 1;
        $request->save();
        $team_detail = TeamDetail::query()->where('team_id', $this->crud->getRequest()->team_id)->get();
        foreach ($team_detail as $item) {
            $user = User::query()->where('id', $item->employee_id)->get();
            $data = $this->crud->getRequest()->message;
            $to_name = $user[0]->name;
            $user_email = $user[0]->email;
            Mail::send('mails.demo_mail', ['request_type'=>$this->crud->getRequest()->request_type,'email'=>backpack_user()->email,'msg' => $data,'user'=>backpack_user()->name], function ($message) use ($to_name, $user_email) {
                $message->to($user_email, $to_name)
                    ->subject('HRMS Mail');
                $message->from(env('MAIL_USERNAME'), 'HRMS');
            });
        }
        return redirect()->route('request.index');
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
        CRUD::setModel(\App\Models\Request::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/request');
        CRUD::setEntityNameStrings('request', 'requests');
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
//        $this->crud->addField([
//            'label' => "Team",
//            'type' => 'select',
//            'name' => 'team_id',
//            'model' => "App\Models\Team",
//            'attribute' => 'name',
//            'options' => (function ($query) {
//                return $query->orderBy('name', 'ASC')->get();
//            }),
//        ]);
        $this->crud->addField([   // n-n relationship
            'label'       => "Cities", // Table column heading
            'type'        => "select2_from_ajax_multiple",
            'name'        => 'cities', // a unique identifier (usually the method that defines the relationship in your Model)
            'entity'      => 'cities', // the method that defines the relationship in your Model
            'attribute'   => "name", // foreign key attribute that is shown to user
            'data_source' => url("api/city"), // url to controller search function (with /{id} should return model)
            'pivot'       => true, // on create&update, do you need to add/delete pivot table entries?

            // OPTIONAL
            'delay' => 500, // the minimum amount of time between ajax requests when searching in the field
            'model'                => "App\Models\City", // foreign key model
            'placeholder'          => "Select a city", // placeholder for the select
            'minimum_input_length' => 2, // minimum characters to type before querying results
            // 'include_all_form_fields'  => false, // optional - only send the current field through AJAX (for a smaller payload if you're not using multiple chained select2s)
        ]);


        $this->crud->addField([   // select_from_array
            'name' => 'request_type',
            'label' => "Request type",
            'type' => 'select_from_array',
            'options' => ['Leave of absence letter' => 'Leave of absence letter', 'late application letter' => 'Late application letter',],
            'allows_null' => false,
            'default' => 'one',
        ]);
        $this->crud->addField([   // select_from_array
            'name' => 'request_type',
            'label' => "Request type",
            'type' => 'select_from_array',
            'options' => ['Leave of absence letter' => 'Leave of absence letter', 'late application letter' => 'Late application letter',],
            'allows_null' => false,
            'default' => 'one',
        ]);

        CRUD::setValidation(RequestRequest::class);
        $this->crud->addField([   // Summernote
                'name' => 'message',
                'label' => 'Message',
                'type' => 'summernote',
                'options' =>
                    [
                        'name' => 'description',
                        'label' => 'Description',
                        'type' => 'summernote',
                        'options' => [
                            'toolbar' => [
                                ['font', ['bold', 'underline', 'italic']]
                            ],
                        ],
                    ]
            ]
        );

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
