<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\RequestRequest;
use App\Jobs\SendWelcomeEmail;
use App\Models\Request;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Str;


/**
 * Class RequestCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class RequestCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation {
        store as traitStore;
    }

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(Request::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/request');
        CRUD::setEntityNameStrings('request', 'requests');
    }

    public function store()
    {
        $request = new Request();
        $request->hash = Str::random(6);
        $request->sender_id = backpack_user()->id;
        $request->message = $this->crud->getRequest()->message;
        $request->state = 1;
        $request->start_date = $this->crud->getRequest()->start_date;
        $request->end_date = $this->crud->getRequest()->end_date;
        $request->save();
        $requestData = $this->crud->getRequest();
        $this->dispatch(new SendWelcomeEmail(collect($requestData)->toArray(), collect(backpack_user())->toArray(), $request->hash));

        return redirect()->route('request.index');
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
            'name' => 'employee',
            'label' => 'T??m theo t??n nh??n vi??n'
        ],
            false,
            function ($value) {
                $this->crud->addClause('whereHas', 'user', function ($query) use ($value) {
                    $query->where('name', 'like', '%' . $value . '%');
                });
            }
        );

        $this->crud->addFilter([
            'type' => 'date',
            'name' => 'date',
            'label' => 'T??m theo ng??y b???t ?????u ngh???'
        ],
            false,
            function ($value) {
                $this->crud->addClause('where', 'start_date', $value);
            });

        CRUD::addColumn([
            'label' => "T??n Nh??n Vi??n",
            'type' => 'select',
            'name' => 'sender_id',
            'entity' => 'user',
            'model' => "App\Models\User",
            'attribute' => 'name',
        ]);

        CRUD::addColumn([
            'name' => 'start_date',
            'type' => 'date_picker',
            'label' => 'Ngh??? T??? ng??y',
            'date_picker_options' => [
                'todayBtn' => 'linked',
                'format' => 'dd-mm-yyyy',
                'language' => 'vi'
            ],
        ]);

        CRUD::addColumn([
            'label' => "Ngh??? ?????n ng??y",
            'name' => 'end_date',
            'type' => 'date_picker',
            'date_picker_options' => [
                'todayBtn' => 'linked',
                'format' => 'dd-mm-yyyy',
                'language' => 'vi'
            ],
        ]);

        CRUD::addColumn([
            'label' => "Tr???ng Th??i",
            'name' => 'state',
            'type' => 'boolean',
            'options' => [1 => '???? Ho??n Th??nh', 0 => 'Ch??a Ho??n Th??nh']
        ]);

        CRUD::addColumn([
            'label' => "N???i Dung",
            'name' => 'message',
            'type' => 'text',

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
        $this->crud->addField([
            'label' => "G???i t???i",
            'type' => 'select2_multiple',
            'name' => 'sendMail',
            'entity' => 'mails',
            'model' => "App\Models\SendMail",
            'attribute' => 'mail_name',
            'pivot' => true,
            'options' => (function ($query) {
                return $query->orderBy('mail_name', 'ASC')->get();
            }),
        ]);

        $this->crud->addFields([
            [
                'name' => 'start_date',
                'type' => 'date_picker',
                'label' => 'T??? ng??y',
                'date_picker_options' => [
                    'todayBtn' => 'linked',
                    'format' => 'dd-mm-yyyy',
                    'language' => 'vi'
                ],
                'wrapper' => [
                    'class' => 'form-group col-md-6'
                ],
            ],

            [
                'label' => "?????n ng??y",
                'name' => 'end_date',
                'type' => 'date_picker',
                'date_picker_options' => [
                    'todayBtn' => 'linked',
                    'format' => 'dd-mm-yyyy',
                    'language' => 'fr'
                ],
                'wrapper' => [
                    'class' => 'form-group col-md-6'
                ],
            ]
        ]);

        CRUD::setValidation(RequestRequest::class);

        $this->crud->addField([
                'name' => 'message',
                'label' => 'N???i dung',
                'type' => 'textarea',
                'attributes' => [
                    'rows' => 5,
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
