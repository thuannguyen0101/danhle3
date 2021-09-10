<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\RequestRequest;
use App\Models\SendMail;
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
        CRUD::setModel(\App\Models\Request::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/request');
        CRUD::setEntityNameStrings('request', 'requests');
    }

    public function store()
    {
        $request = new Request();
        $request->sender_id = backpack_user()->id;
        $request->message = $this->crud->getRequest()->message;
        $request->state = 1;
        $request->start_date = $this->crud->getRequest()->start_date;
        $request->end_date = $this->crud->getRequest()->end_date;
        $array_mail = array();
        foreach ($this->crud->getRequest()->mail as $sendmail) {
            $mail = SendMail::find($sendmail);
            if ($mail->team_id == null) {
                $user = User::query()->where('email', $mail->mail_name)->get();
                $to_name = $user[0]->name;
                $user_email = $mail->mail_name;
                array_push($array_mail, $mail->mail_name);
                Mail::send('mails.demo_mail', ['user' => $user[0], 'content' => $this->crud->getRequest()], function ($message) use ($to_name, $user_email) {
                    $message->to($user_email, $to_name)
                        ->subject('ĐƠN XIN NGHỈ PHÉP CỦA :' . backpack_user()->name);
                    $message->from(env('MAIL_USERNAME'), 'HRMS');
                });
            } else {
                $team_detail = TeamDetail::query()->where('team_id', $mail->team_id)->get();
                foreach ($team_detail as $item) {
                    $user = User::query()->where('id', $item->employee_id)->get();
                    if (!in_array($user[0]->email, $array_mail)) {
                        $to_name = $user[0]->name;
                        $user_email = $user[0]->email;
                        Mail::send('mails.demo_mail', ['user' => $user[0], 'content' => $this->crud->getRequest()], function ($message) use ($to_name, $user_email) {
                            $message->to($user_email, $to_name)
                                ->subject('ĐƠN XIN NGHỈ PHÉP CỦA :' . backpack_user()->name);
                            $message->from(env('MAIL_USERNAME'), 'HRMS');
                        });
                    }
                }

            }
        }
        $request->save();

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
            'label' => 'Tìm theo tên nhân viên'
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
            'label' => 'Tìm theo ngày bắt đầu nghỉ'
        ],
            false,
            function ($value) {
                $this->crud->addClause('where', 'start_date', $value);
            });

        CRUD::addColumn([
            'label' => "Tên Nhân Viên",
            'type' => 'select',
            'name' => 'sender_id',
            'entity' => 'user',
            'model' => "App\Models\User",
            'attribute' => 'name',
        ]);

        CRUD::addColumn([
            'name' => 'start_date',
            'type' => 'date_picker',
            'label' => 'Nghỉ Từ ngày',
            'date_picker_options' => [
                'todayBtn' => 'linked',
                'format' => 'dd-mm-yyyy',
                'language' => 'vi'
            ],
        ]);

        CRUD::addColumn([
            'label' => "Nghỉ Đến ngày",
            'name' => 'end_date',
            'type' => 'date_picker',
            'date_picker_options' => [
                'todayBtn' => 'linked',
                'format' => 'dd-mm-yyyy',
                'language' => 'vi'
            ],
        ]);

        CRUD::addColumn([
            'label' => "Trạng Thái",
            'name' => 'state',
            'type' => 'boolean',
            'options' => [1 => 'Đã Hoàn Thành', 0 => 'Chưa Hoàn Thành']
        ]);
        CRUD::addColumn([
            'label' => "Nội Dung",
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
            'label' => "Gủi tới",
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
                'label' => 'Từ ngày',
                'date_picker_options' => [
                    'todayBtn' => 'linked',
                    'format' => 'dd-mm-yyyy',
                    'language' => 'vi'
                ],
                'wrapper'   => [
                    'class'      => 'form-group col-md-6'
                ],
            ],
            [
                'label' => "Đến ngày",
                'name' => 'end_date',
                'type' => 'date_picker',
                'date_picker_options' => [
                    'todayBtn' => 'linked',
                    'format' => 'dd-mm-yyyy',
                    'language' => 'fr'
                ],
                'wrapper'   => [
                    'class'      => 'form-group col-md-6'
                ],
            ]
        ]);

        CRUD::setValidation(RequestRequest::class);
        $this->crud->addField([
                'name' => 'message',
                'label' => 'Nội dung',
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
