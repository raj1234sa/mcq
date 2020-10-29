<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

function get_sidebar_links()
{
    $links = array();
    $links[] = array(
        'title' => 'Dashboard',
        'route' => '/dashboard',
        'icon' => 'zmdi zmdi-home',
    );
    $links[] = array(
        'title' => 'Department',
        'icon' => 'zmdi zmdi-folder',
        'children' => array(
            array(
                'title' => 'Departments',
                'route' => '/department-list',
            ),
        ),
    );
    $links[] = array(
        'title' => 'Category',
        'icon' => 'zmdi zmdi-folder',
        'children' => array(
            array(
                'title' => 'Categories',
                'route' => '/category-list',
            ),
        ),
    );
    $links[] = array(
        'title' => 'Subject',
        'icon' => 'zmdi zmdi-view-week',
        'children' => array(
            array(
                'title' => 'Subjects',
                'route' => '/subject-list',
            ),
        ),
    );
    return $links;
}

function draw_action_buttons($actions)
{
    $html = '';
    foreach ($actions as $title => $button) {
        $html .= "<a class='btn {$button['class']} btn--icon-text' href='" . URL::to($button['route']) . "'><i class='{$button['icon']}'></i>{$title}</a>";
    }
    return $html;
}

function draw_form_buttons($actions, $backlink)
{
    $html = '';
    foreach ($actions as $button) {
        switch ($button) {
            case 'save':
                $html .= "<button class='btn btn-primary btn--icon-text form-save'><i class='zmdi zmdi-floppy'></i>Save</button>";
                break;
            case 'save_back':
                $html .= "<button class='btn btn-primary btn--icon-text form-save-back'><i class='zmdi zmdi-floppy'></i>Save & Back</button>";
                break;
            case 'reset':
                $html .= "<button class='btn btn-secondary btn--icon-text form-reset'><i class='zmdi zmdi-refresh-alt'></i>Reset</button>";
                break;
            default:
                break;
        }
    }
    $html .= "<a class='btn btn-secondary btn--icon-text' href='$backlink'><i class='zmdi zmdi-arrow-left'></i>Back</a>";
    return $html;
}

function draw_action_menu($action_links)
{
    $html = '';
    $html .= '<div class="btn-group">
                <button type="button" class="btn btn-light">Action</button>
                <button type="button" class="btn btn-light dropdown-toggle dropdown-toggle-split" data-toggle="dropdown">
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-right action-menu" x-placement="bottom-end">';
    foreach ($action_links as $key => $value) {
        $class = '';
        if (isset($value['class'])) {
            $class = $value['class'];
        }
        $html .= "<li>
                    <a href='{$value['link']}' class='$class dropdown-item'><i class='{$value['icon']}'></i>$key</a>
                </li>";
    }
    $html .= '</ul></div>';
    return $html;
}

function form_element($label, $name, $type, $value='', $extra = array())
{
    $html = '';
    $id = $name;
    $div_class = '';
    if (isset($extra['id']) || !empty($extra['id'])) {
        $id = $extra['id'];
    }
    if (isset($extra['frm_grp_class'])) {
        $div_class = $extra['frm_grp_class'];
    }
    if(isset($extra['form_group']) && $extra['form_group'] == false) {
        $html .= "<div class='position-relative $div_class'>";
    } else {
        $html .= "<div class='form-group col-12 $div_class'>";
    }
    if (!empty($label)) {
        $html .= "<label>$label</label>";
    }
    $placeholder = '';
    if(isset($extra['hint'])) {
        $placeholder = $extra['hint'];
    }
    $html .= "<input type='$type' value='$value' name='$name' id='$id' class='form-control' placeholder='$placeholder'>";
    $html .= '<i class="form-group__bar"></i>';
    $html .= "</div>";
    return $html;
}

function form_select($label, $name, $value, $extra = array())
{
    $html = '';
    $id = $name;
    $div_class = '';
    if (isset($extra['id']) || !empty($extra['id'])) {
        $id = $extra['id'];
    }
    if (isset($extra['frm_grp_class'])) {
        $div_class = $extra['frm_grp_class'];
    }
    if(isset($extra['form_group']) && $extra['form_group'] == false) {
        $html .= "<div class='position-relative $div_class'>";
    } else {
        $html .= "<div class='form-group col-12 $div_class'>";
    }
    if (!empty($label)) {
        $html .= "<label>$label</label>";
    }
    $attributes = '';
    if (isset($extra['attributes']) && !empty($extra['attributes'])) {
        $attributes = $extra['attributes'];
    }
    if (isset($extra['searchdropdown']) && $extra['searchdropdown'] == false) {
        $attributes .= "data-minimum-results-for-search='Infinity'";
    }
    $html .= "<select class='select2' id='$id' name='$name' $attributes>";

    if (isset($extra['list_before']) && !empty($extra['list_before'])) {
        $html .= $extra['list_before'];
    }

    $dropdownArr = $extra['list'];
    foreach ($dropdownArr as $value1) {
        $selected = (!empty($value) && $value == $value1[$extra['value_field']]) ? "selected='selected'" : '';
        $html .= "<option value='" . $value1[$extra['value_field']] . "' $selected>" . $value1[$extra['text_field']] . "</option>";
    }
    $html .= "</select>";
    $html .= "</div>";
    return $html;
}

function draw_switchbutton($label, $name, $value, $extra = array())
{
    $html = '';
    $id = $name;
    $div_class = '';
    if (isset($extra['id']) || !empty($extra['id'])) {
        $id = $extra['id'];
    }
    if (isset($extra['frm_grp_class'])) {
        $div_class = $extra['frm_grp_class'];
    }
    if ($extra['form_group'] != false) {
        $html .= "<div class='form-group col-12 $div_class'>";
    }
    if (!empty($label)) {
        $html .= "<label>$label</label>";
    }
    $html = "<div class='toggle-switch toggle-switch--blue'>";
    $html .= "<input type='checkbox' class='toggle-switch__checkbox' value='$value' name='$name' id='$id'>";
    $html .= "<i class='toggle-switch__helper'></i>";
    $html .= "</div>";
    if ($extra['form_group'] != false) {
        $html .= "</div>";
    }
    return $html;
}

function draw_options($list, $value_field, $text_field, $selected) {
    $html = '';
    foreach ($list as $value) {
        $select = ($selected == $value[$value_field]) ? "selected='selected'" : '';
        $html .= "<option value='" . $value[$value_field] . "' $select>" . $value[$text_field] . "</option>";
    }
    return $html;
}

function getDetails($list, $keys = array()) {
    $html = '';
    $i = 1;
    foreach ($keys as $key => $val) {
        $html .= "<strong>".$val['title'].":</strong> ".$list[$key];
        if(count($keys) != $i) {
            $html .= "<br>";
        }
        $i++;
    }
    return $html;
}

function draw_disabled_dropdown($status) {
    if(!$status) {
        return " (X)";
    }
    return '';
}
?>
