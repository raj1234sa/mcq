<?php
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
        $html .= "<a class='btn {$button['class']} btn--icon-text' href='".URL::to($button['route'])."'><i class='{$button['icon']}'></i>{$title}</a>";
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

function form_element($label, $name, $type, $value, $extra = array()) {
    $html = '';
    $id = $name;
    $div_class = '';
    if(isset($extra['id']) || !empty($extra['id'])) {
        $id = $extra['id'];
    }
    if(isset($extra['frm_grp_class'])) {
        $div_class = $extra['frm_grp_class'];
    }
    $html .= "<div class='form-group col-12 $div_class'>";
    if(!empty($label)) {
        $html .= "<label>$label</label>";
    }
    $html .= "<input type='$type' value='$value' name='$name' id='$id' class='form-control'>";
    $html .= '<i class="form-group__bar"></i>';
    $html .= "</div>";
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
?>
