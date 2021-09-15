<?php

namespace App\Http\Controllers\Admin;
use App\Http\Requests\TeamRequest;
use App\Models\SendMail;
use App\Models\Team;
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
        CRUD::setModel(\App\Models\Team::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/team');
        CRUD::setEntityNameStrings('team', 'teams');
    }

    public function store()
    {
        $team = new Team();
        $team->fill($this->crud->getRequest()->all());
        $unicode = $this->unicode();
        $team->save();
        $str = $this->crud->getRequest()->name;
        foreach ($unicode as $nonUnicode => $uni) {
            $str = preg_replace("/($uni)/i", $nonUnicode, $str);
        }
        $mail_name = 'team' . $str . '@newit.co.jp';
        $mail_name = str_replace(' ', '', $mail_name);
        $mail = new SendMail();
        $mail->mailName = $mail_name;
        $mail->teamId = $team->id;
        $mail->save();
        return redirect()->route('user.index');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        $this->crud->addColumn([
            'name' => 'name',
            'label' => "Tên Nhóm",
            'type' => 'text'
        ]);

        $this->crud->addColumn([
            'label' => 'Phòng',
            'type' => 'select',
            'name' => 'department_id',
            'entity' => 'department',
            'attribute' => 'name',
            'model' => "App\Models\Department",
        ]);

        $this->crud->addColumn([
            'label' => 'Trưởng Nhóm',
            'type' => 'select',
            'name' => 'leader_id',
            'entity' => 'leader',
            'attribute' => 'name',
            'model' => "App\Models\User",
        ]);
        $this->crud->addColumn([
            'name' => 'description',
            'label' => "Mô Tả",
            'type' => 'text'
        ]);

        $this->crud->addColumn([
            'name' => 'status',
            'label' => "Trạng Thái",
            'type' => 'boolean',
            'options' => [0 => 'Ngừng Hoạt Động', 1 => 'Đang Hoạt Động']
        ]);

        $this->crud->addFilter([
            'name' => 'name',
            'type' => 'dropdown',
            'label' => 'Tìm theo tên phòng'
        ], function () {
            return \App\Models\Department::all()->pluck('name', 'id')->toArray();
        }, function ($value) {
            $this->crud->addClause('where', 'department_id', $value);
        });

        $this->crud->addFilter([
            'type' => 'text',
            'name' => 'leader',
            'label' => 'Tìm theo tên trưởng nhóm'
        ], false,
            function ($value) {
                $this->crud->addClause('whereHas', 'leader', function ($query) use ($value) {
                    $query->where('name', 'like', '%' . $value . '%');
                });
            }
        );
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

        $this->crud->addFields([
            [
                'name' => 'name',
                'label' => 'Tên nhóm',
                'type' => 'text',
                'wrapper' => [
                    'class' => 'form-group col-md-12'
                ],
            ],
            [
                'label' => "Trường Phòng",
                'type' => 'select',
                'name' => 'leader_id',
                'model' => "App\Models\User",
                'attribute' => 'name',
                'options' => (function ($query) {
                    return $query->orderBy('name', 'ASC')->get();
                }),
                'wrapper' => [
                    'class' => 'form-group col-md-6'
                ],
            ],
            [
                'label' => "Phòng ban",
                'type' => 'select',
                'name' => 'department_id',
                'model' => "App\Models\Department",
                'attribute' => 'name',
                'options' => (function ($query) {
                    return $query->orderBy('name', 'ASC')->get();
                }),
                'wrapper' => [
                    'class' => 'form-group col-md-6'
                ]
            ],
            [
                'name' => 'description',
                'label' => 'Mô tả',
                'type' => 'textarea',
                'wrapper' => [
                    'class' => 'form-group col-md-12'
                ],
                'attributes' => [
                    'rows' => 5,
                ]
            ],
            [
                'name' => 'status',
                'label' => 'Trạng thái nhóm',
                'type' => 'radio',
                'options' => [
                    0 => "Chưa đi vào hoạt động",
                    1 => "Đang hoạt động"
                ],
                'default' => 1
            ],
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

    public function unicode()
    {
        return array(
            'a' => 'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
            'd' => 'đ',
            'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i' => 'í|ì|ỉ|ĩ|ị',
            'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
            'A' => 'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
            'D' => 'Đ',
            'E' => 'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
            'I' => 'Í|Ì|Ỉ|Ĩ|Ị',
            'O' => 'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
            'U' => 'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
            'Y' => 'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
        );
    }
}
