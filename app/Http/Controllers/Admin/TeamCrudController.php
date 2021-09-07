<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\TeamRequest;
use App\Models\Team;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Mail;

/**
 * Class TeamCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class TeamCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation {
        store as traitStore;
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
        $mail_name = 'team'.$str.'@newit.co.jp';
        $mail_name = str_replace(' ', '', $mail_name);
        $mail = new \App\Models\mail();
        $mail->mail_name = $mail_name;
        $mail->team_id = $team->id;
        $mail->save();
        return redirect()->route('user.index');
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
    public
    function setup()
    {
        CRUD::setModel(\App\Models\Team::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/team');
        CRUD::setEntityNameStrings('team', 'teams');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected
    function setupListOperation()
    {
//        CRUD::setFromDb(); // columns


        $this->crud->addColumn([
            'label' => 'Department', // Table column heading
            'type' => 'select',
            'name' => 'department_id', // the column that contains the ID of that connected entity;
            'entity' => 'department', // the method that defines the relationship in your Model
            'attribute' => 'name', // foreign key attribute that is shown to user
            'model' => "App\Models\Department", // foreign key model
        ]);

        $this->crud->addColumn([
            'label' => 'Leader', // Table column heading
            'type' => 'select',
            'name' => 'leader_id', // the column that contains the ID of that connected entity;
            'entity' => 'leader', // the method that defines the relationship in your Model
            'attribute' => 'name', // foreign key attribute that is shown to user
            'model' => "App\Models\User", // foreign key model
        ]);
        $this->crud->addColumn([
            'name' => 'name', // the db column name (attribute name)
            'label' => "Name", // the human-readable label for it
            'type' => 'text' // the kind of column to show
        ]);
        $this->crud->addColumn([
            'name' => 'description', // the db column name (attribute name)
            'label' => "Description", // the human-readable label for it
            'type' => 'text' // the kind of column to show
        ]);

        $this->crud->addColumn([
            'name' => 'status', // the db column name (attribute name)
            'label' => "Status", // the human-readable label for it
            'type' => 'boolean', // the kind of column to show
            'options' => [0 => 'Inactive', 1 => 'Active']
        ]);


        $this->crud->addFilter([
            'type' => 'text',
            'name' => 'description',
            'label' => 'Description'
        ],
            false,
            function ($value) { // if the filter is active
                $this->crud->addClause('where', 'description', 'LIKE', "%$value%");
            });

        $this->crud->addFilter([
            'name' => 'name',
            'type' => 'dropdown',
            'label' => 'Name Department'
        ], function () {
            return \App\Models\Department::all()->pluck('name', 'id')->toArray();
        }, function ($value) { //
            $this->crud->addClause('where', 'department_id', $value);
        });

        $this->crud->addFilter([
            'type' => 'text',
            'name' => 'leader',
            'label' => 'Search by name Leader'
        ],
            false,
            function ($value) {
                $this->crud->addClause('whereHas', 'leader', function ($query) use ($value) {
                    $query->where('name', 'like', '%' . $value . '%');
                });
            }
        );


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
    protected
    function setupCreateOperation()
    {
        CRUD::setValidation(TeamRequest::class);

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
    protected
    function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
    public function unicode(){
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
