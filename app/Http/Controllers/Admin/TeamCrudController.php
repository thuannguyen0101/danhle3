<?php

namespace App\Http\Controllers\Admin;
use App\Http\Requests\TeamRequest;
use App\Models\Department;
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
        CRUD::setModel(Team::class);
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
        $mail_name = sprintf('team%s@newit.co.jp',$str);
        $mail_name = str_replace(' ', '', $mail_name);
        $mail = new SendMail();
        $mail->mail_name = $mail_name;
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
            'label' => "T??n Nh??m",
            'type' => 'text'
        ]);

        $this->crud->addColumn([
            'label' => 'Ph??ng',
            'type' => 'select',
            'name' => 'department_id',
            'entity' => 'department',
            'attribute' => 'name',
            'model' => "App\Models\Department",
        ]);

        $this->crud->addColumn([
            'label' => 'Tr?????ng Nh??m',
            'type' => 'select',
            'name' => 'leader_id',
            'entity' => 'leader',
            'attribute' => 'name',
            'model' => "App\Models\User",
        ]);
        $this->crud->addColumn([
            'name' => 'description',
            'label' => "M?? T???",
            'type' => 'text'
        ]);

        $this->crud->addColumn([
            'name' => 'status',
            'label' => "Tr???ng Th??i",
            'type' => 'boolean',
            'options' => [0 => 'Ng???ng Ho???t ?????ng', 1 => '??ang Ho???t ?????ng']
        ]);

        $this->crud->addFilter([
            'name' => 'name',
            'type' => 'dropdown',
            'label' => 'T??m theo t??n ph??ng'
        ], function () {
            return Department::all()->pluck('name', 'id')->toArray();
        }, function ($value) {
            $this->crud->addClause('where', 'department_id', $value);
        });

        $this->crud->addFilter([
            'type' => 'text',
            'name' => 'leader',
            'label' => 'T??m theo t??n tr?????ng nh??m'
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
                'label' => 'T??n nh??m',
                'type' => 'text',
                'wrapper' => [
                    'class' => 'form-group col-md-12'
                ],
            ],
            [
                'label' => "Tr?????ng Ph??ng",
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
                'label' => "Ph??ng ban",
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
                'label' => 'M?? t???',
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
                'label' => 'Tr???ng th??i nh??m',
                'type' => 'radio',
                'options' => [
                    0 => "Ch??a ??i v??o ho???t ?????ng",
                    1 => "??ang ho???t ?????ng"
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
            'a' => '??|??|???|??|???|??|???|???|???|???|???|??|???|???|???|???|???',
            'd' => '??',
            'e' => '??|??|???|???|???|??|???|???|???|???|???',
            'i' => '??|??|???|??|???',
            'o' => '??|??|???|??|???|??|???|???|???|???|???|??|???|???|???|???|???',
            'u' => '??|??|???|??|???|??|???|???|???|???|???',
            'y' => '??|???|???|???|???',
            'A' => '??|??|???|??|???|??|???|???|???|???|???|??|???|???|???|???|???',
            'D' => '??',
            'E' => '??|??|???|???|???|??|???|???|???|???|???',
            'I' => '??|??|???|??|???',
            'O' => '??|??|???|??|???|??|???|???|???|???|???|??|???|???|???|???|???',
            'U' => '??|??|???|??|???|??|???|???|???|???|???',
            'Y' => '??|???|???|???|???',
        );
    }
}
