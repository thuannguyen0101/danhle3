<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\RequestRequest;
use App\Models\mail;
use App\Models\Request;
use App\Models\TeamDetail;
use App\Models\User;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;


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
            Mail::send('mails.demo_mail', ['request_type' => $this->crud->getRequest()->request_type, 'email' => backpack_user()->email, 'msg' => $data, 'user' => backpack_user()->name], function ($message) use ($to_name, $user_email) {
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


        $this->crud->addField([
            'label' => "Gủi tới",
            'type' => 'select2_multiple',
            'name' => 'tags',
            'entity' => 'mail',
            'model' => "App\Models\mail",
            'attribute' => 'mail_name',
            'pivot' => true,
            'options' => (function ($query) {
                return $query->orderBy('mail_name', 'ASC')->get();
            }),
        ]);


        $this->crud->addFields([
            [
                'label' => "Bắt đầu nghỉ từ ngày",
                'type' => 'date',
                'name' => 'start_date',
            ],
            [
                'label' => "Đến ngày",
                'type' => 'date',
                'name' => 'end_date',
            ]
        ]);


        CRUD::setValidation(RequestRequest::class);
        $this->crud->addField([   // Summernote
                'name' => 'message',
                'label' => 'Nội dung',
                'type' => 'textarea',
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
