<?php

namespace App\Http\Controllers;

use App\Models\Examination;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    private $fieldArr = array(
        'id',
        'exam_name',
        'categories.category_name',
        'subjects.subject_name',
        'deadline',
        'status',
    );

    public function index()
    {
        $action_button = array();
        $action_button['Add Question'] = array(
            'icon' => 'zmdi zmdi-plus',
            'route' => '/question-add',
            'class' => 'btn-success'
        );
        $examinations = Examination::get();
        foreach ($examinations as $value) {
            $value->exam_name .= draw_disabled_dropdown($value['status']);
        }
        $data = array(
            'route' => "/question-list",
            'actions' => $action_button,
            'examinations' => $examinations,
        );
        return view('questions.index', $data);
    }

    public function create()
    {
        $subjects = Subject::get();
        foreach ($subjects as $value) {
            $value->department_name .= draw_disabled_dropdown($value['status']);
        }
        $categories = Category::get();
        foreach ($categories as $value) {
            $value->category_name .= draw_disabled_dropdown($value['status']);
        }
        $data = array(
            'route' => '/exam-list',
            'form_buttons' => true,
            'subjects' => $subjects,
            'categories' => $categories,
            'backlink' => '/exam-list',
        );
        return view('examination.add', $data);
    }

    public function save(Request $req)
    {
        $exam_name = $req->input('exam_name');
        $duration = $req->input('duration');
        $passmark = $req->input('passmark');
        $deadline = $req->input('deadline');
        $category_id = $req->input('category_id');
        $subject_id = $req->input('subject_id');
        $terms_conditions = $req->input('terms_conditions');
        $this_action = $req->input('this_action');
        $submit_btn = $req->input('submit_btn');
        if (!empty($this_action)) {
            $subject = Examination::where('id', '=', $this_action)->get();
            $subject = $subject[0];
        } else {
            $subject = new Examination();
        }
        $subject->exam_name = $exam_name;
        $subject->duration = $duration;
        $subject->passmark = $passmark;
        $subject->deadline = $deadline;
        $subject->category_id = $category_id;
        $subject->subject_id = $subject_id;
        $subject->terms_conditions = $terms_conditions;
        $subject->status = '1';
        if ($subject->save()) {
            if (isset($this_action) && !empty($this_action)) {
                if ($submit_btn == 'save')
                    return redirect("/exam-add/" . $subject->id)->with('success', 'Examination is update successfully.');
                if ($submit_btn == 'save_back')
                    return redirect("/exam-list")->with('success', 'Examination is update successfully.');
            } else {
                if ($submit_btn == 'save')
                    return redirect("/exam-add/" . $subject->id)->with('success', 'Examination is added successfully.');
                if ($submit_btn == 'save_back')
                    return redirect("/exam-list")->with('success', 'Examination is added successfully.');
            }
        }
        if ($this_action == "edit") {
            return redirect()->back()->with('fail', 'Error updating examination.');
        } else {
            return redirect()->back()->with('fail', 'Error adding examination.');
        }
    }

    public function edit($id)
    {
        $examination = Examination::find($id);
        $subjects = Subject::get();
        foreach ($subjects as $value) {
            $value->department_name .= draw_disabled_dropdown($value['status']);
        }
        $categories = Category::get();
        foreach ($categories as $value) {
            $value->category_name .= draw_disabled_dropdown($value['status']);
        }
        $data = array(
            'route' => '/subject-list',
            'form_buttons' => true,
            'post_data' => $examination,
            'this_action' => 'edit',
            'subjects' => $subjects,
            'categories' => $categories,
            'backlink' => '/subject-list',
        );
        return view('examination.add', $data);
    }

    public function getExams(Request $req)
    {
        $fieldArr = $this->fieldArr;
        $order = $req->input('order')[0]['column'];
        $dir = $req->input('order')[0]['dir'];
        $totalRec = count(Examination::get());
        $search = extract_search_field($req->input('data'));

        $start = $req->input('start');
        $length = $req->input('length');
        $examinations = Examination::offset($start)->limit($length);
        $sortFields = explode(',', $fieldArr[$order]);
        foreach ($sortFields as $value) {
            $examinations->orderBy($value, $dir);
        }
        if(!empty($search['keywords'])) {
            $examinations->where("exam_name", "LIKE", "%{$search['keywords']}%");
        }
        if(!empty($search['subject_id'])) {
            $examinations->where("subject_id", "=", $search['subject_id']);
        }
        if(!empty($search['category_id'])) {
            $examinations->where("category_id", "=", $search['category_id']);
        }
        $examinations = $examinations->get();
        
        $sr = $start + 1;
        $htmlArray = array();
        foreach ($examinations as $exam) {
            $exam->subject;
            $exam->category;

            $rec = array();
            $rec['DT_RowId'] = 'sub:' . $exam['id'];
            $rec[] = $sr;
            $rec[] = $exam['exam_name'];
            $rec[] = $exam['category']['category_name'];
            $rec[] = $exam['subject']['subject_name'];
            $rec[] = setDateFormat($exam['deadline'], 'd M Y');
            $action_links = array();
            $action_links['Edit'] = array(
                'icon' => 'far fa-edit',
                'link' => '/exam-add/' . $exam['id'],
            );
            $action_links['Delete'] = array(
                'class' => 'label-danger ajax delete',
                'icon' => 'far fa-trash-alt',
                'link' => '/exam-delete/' . $exam['id'],
            );
            $currentStatus = $exam['status'] == '1' ? '<span class="text-success">ACTIVE</span>' : '<span class="text-danger">INACTIVE</span>';
            $statusButton = $exam['status'] == '1' ? "<button class='btn btn-dark btn--icon ml-2 ajax change_status' data-status='0' data-url='/exam-status' data-id='{$exam['id']}' title='Deactivate'><i class='zmdi zmdi-close'></i></button>" : "<button class='btn btn-dark btn--icon ml-2 ajax change_status' data-status='1' data-url='/exam-status' data-id='{$exam['id']}' title='Activate'><i class='zmdi zmdi-check'></i></button>";
            $rec[] = $currentStatus . $statusButton;
            $rec[] = draw_action_menu($action_links);
            $htmlArray[] = $rec;
            $sr++;
        }
        return array(
            'data' => $htmlArray,
            'recordsTotal' => $totalRec,
            'recordsFiltered' => $totalRec,
            'draw' => $req->input('draw'),
        );
    }

    public function deleteExam($id)
    {
        if (!empty($id)) {
            $examination = Examination::find($id);
            if ($examination->delete()) {
                $response = new Response('success');
            } else {
                $response = new Response('Cannot delete examination');
            }
            return $response;
        }
    }

    public function changeStatus(Request $req)
    {
        $id = $req->input('id');
        $status = $req->input('status');

        $examination = Examination::find($id);
        $examination->status = $status;
        if ($examination->save()) {
            $response = new Response('success');
        } else {
            $response = new Response('Cannot change status of examination');
        }
        return $response;
    }

    public function getSubjectCategory(Request $req)
    {
        $subj_id = $req->input('subj_id');
        $categories = Subject::where('subjects.id', '=', $subj_id)
        ->select("categories.category_name","categories.id")
        ->leftjoin("categories", "categories.id", "=", "subjects.category_id")
        ->get();
        $categories = objectToArray($categories);
        return draw_options($categories, 'id', 'category_name', $req->input('selected'),"<option value=''>Select Category</option>");
    }

    public function export(Request $req) {
        $fieldArr = $this->fieldArr;

        $order = $req->input('column');
        $dir = $req->input('dir');

        $search = extract_search_field($req->input('data'));

        $examinations = Examination::orderBy($fieldArr[$order], $dir);
        $examinations->select("examinations.*", "subjects.subject_name", "categories.category_name", DB::raw("IF(examinations.status = '1', 'Enabled', 'Disabled') as status"));

        if(!empty($search['keywords'])) {
            $examinations->where("exam_name", "LIKE", "%{$search['keywords']}%");
        }
        if(!empty($search['subject_id'])) {
            $examinations->where("examinations.subject_id", "=", $search['subject_id']);
        }
        if(!empty($search['category_id'])) {
            $examinations->where("examinations.category_id", "=", $search['category_id']);
        }

        $examinations->leftjoin("subjects", "subjects.id", "=", "examinations.subject_id");
        $examinations->leftjoin("categories", "categories.id", "=", "examinations.category_id");

        $examinations = $examinations->get();

        $export_structure = array();
        $export_structure[] = array('id'=>array('name'=>'id', 'title'=>'Examination ID'));
        $export_structure[] = array('exam_name'=>array('name'=>'exam_name', 'title'=>'Exam Name'));
        $export_structure[] = array('category_name'=>array('name'=>'category_name', 'title'=>'Category Name'));
        $export_structure[] = array('subject_name'=>array('name'=>'subject_name', 'title'=>'Subject Name'));
        $export_structure[] = array('duration'=>array('name'=>'duration', 'title'=>'Duration', 'call_func'=>'addPostfix', 'func_param'=>array('duration', ' Minutes')));
        $export_structure[] = array('passmark'=>array('name'=>'passmark', 'title'=>'Passmark', 'call_func'=>'addPostfix', 'func_param'=>array('passmark', '%')));
        $export_structure[] = array('deadline'=>array('name'=>'deadline', 'title'=>'Deadline', 'call_func'=>'setDateFormat', 'func_param'=>array('deadline', 'd M Y')));
        $export_structure[] = array('terms_conditions'=>array('name'=>'terms_conditions', 'title'=>'Terms & Conditions'));
        $export_structure[] = array('status'=>array('name'=>'status', 'title'=>'Status'));

        $spreadsheet = export_file_generate($export_structure, $examinations, array(
            'headerDate' => 'All',
            'sheetTitle' => 'Examinations Report',
        ));

        return export_report($spreadsheet, 'export_examinations.xlsx');
    }
}
