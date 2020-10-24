<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CategoryController extends Controller
{
    public function index()
    {
        $action_button = array();
        $action_button['Add Category'] = array(
            'icon' => 'zmdi zmdi-plus',
            'route' => '/category-add',
            'class' => 'btn-success'
        );
        $data = array(
            'route' => "/category-list",
            'actions' => $action_button,
        );
        return view('categories.index', $data);
    }

    public function create()
    {
        $departments = Department::where("status", "=", "1")->get();
        $data = array(
            'route' => '/category-list',
            'form_buttons' => true,
            'departments' => $departments,
            'backlink' => '/category-list',
        );
        return view('categories.add', $data);
    }

    public function save(Request $req)
    {
        $category_name = $req->input('category_name');
        $department_id = $req->input('department_id');
        $this_action = $req->input('this_action');
        $submit_btn = $req->input('submit_btn');
        if (!empty($this_action)) {
            $category = Category::where('id', '=', $this_action)->get();
            $category = $category[0];
        } else {
            $category = new Category();
        }
        $category->category_name = $category_name;
        $category->department_id = $department_id;
        $category->status = '1';
        if ($category->save()) {
            if (isset($this_action) && !empty($this_action)) {
                if ($submit_btn == 'save')
                    return redirect("/category-add/" . $category->id)->with('success', 'Category is update successfully.');
                if ($submit_btn == 'save_back')
                    return redirect("/category-list")->with('success', 'Category is update successfully.');
            } else {
                if ($submit_btn == 'save')
                    return redirect("/category-add/" . $category->id)->with('success', 'Category is added successfully.');
                if ($submit_btn == 'save_back')
                    return redirect("/category-list")->with('success', 'Category is added successfully.');
            }
        }
        if ($this_action == "edit") {
            return redirect()->back()->with('fail', 'Error updating category.');
        } else {
            return redirect()->back()->with('fail', 'Error adding category.');
        }
    }

    public function edit($id)
    {
        $category = Category::where('id', '=', $id)->get();
        $category = $category[0];
        $departments = Department::where("status", "=", "1")->get();
        $data = array(
            'route' => '/category-list',
            'form_buttons' => true,
            'post_data' => $category,
            'this_action' => 'edit',
            'departments' => $departments,
            'backlink' => '/category-list',
        );
        return view('categories.add', $data);
    }

    public function getCategories(Request $req)
    {
        $fieldArr = array(
            'id',
            'category_name',
            'department_name',
            'status',
        );
        $order = $req->input('order')[0]['column'];
        $dir = $req->input('order')[0]['dir'];
        $totalRec = count(Category::get());
        $start = $req->input('start');
        $length = $req->input('length');
        $categories = Category::offset($start)->limit($length);
        $categories->orderBy($fieldArr[$order], $dir);
        $categories->select('categories.*', 'departments.department_name');
        $categories->join('departments', 'departments.id', '=', 'categories.department_id');
        $categories = $categories->get();
        $sr = $start + 1;
        $htmlArray = array();
        foreach ($categories as $key => $dept) {
            $rec = array();
            $rec['DT_RowId'] = 'cat:' . $dept['id'];
            $rec[] = $sr;
            $rec[] = $dept['category_name'];
            $rec[] = $dept['department_name'];
            $action_links = array();
            $action_links['Edit'] = array(
                'icon' => 'far fa-edit',
                'link' => '/category-add/' . $dept['id'],
            );
            $action_links['Delete'] = array(
                'class' => 'label-danger ajax delete',
                'icon' => 'far fa-trash-alt',
                'link' => '/category-delete/' . $dept['id'],
            );
            $currentStatus = $dept['status'] == '1' ? '<span class="text-success">ACTIVE</span>' : '<span class="text-danger">INACTIVE</span>';
            $statusButton = $dept['status'] == '1' ? "<button class='btn btn-dark btn--icon ml-2 ajax change_status' data-status='0' data-url='/category-status' data-id='{$dept['id']}' title='Deactivate'><i class='zmdi zmdi-close'></i></button>" : "<button class='btn btn-dark btn--icon ml-2 ajax change_status' data-status='1' data-url='/category-status' data-id='{$dept['id']}' title='Activate'><i class='zmdi zmdi-check'></i></button>";
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

    public function deleteCategory($id)
    {
        if (!empty($id)) {
            $category = Category::find($id);
            $category = $category[0];
            if ($category->delete()) {
                $response = new Response('success');
            } else {
                $response = new Response('Cannot delete category');
            }
            return $response;
        }
    }

    public function changeStatus(Request $req)
    {
        $id = $req->input('id');
        $status = $req->input('status');

        $category = Category::find($id);
        $category->status = $status;
        if ($category->save()) {
            $response = new Response('success');
        } else {
            $response = new Response('Cannot change status of category');
        }
        return $response;
    }
}
