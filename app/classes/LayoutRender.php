<?php

/**
 * Description of LayoutRender
 *
 * @author Alaa
 */
class LayoutRender
{

    private $db;
    private $f3;
    private $layout;
    private $objUser;
    private $asideMenu;

    public function __construct($objUser, $db)
    {
        $this->f3 = \Base::instance();
        $this->db = $db;
        $this->objUser = $objUser;

        $this->layout = $objUser->menuLayout;
        if (!$objUser->menuLayout) {
            switch ($objUser->typeId) {
                case 1:
                    $this->layout = "admin";
                    break;

                default:
                    $this->layout = "user";
                    break;
            }
        }

        $this->f3->set('objUser', $this->objUser);
    }

    public function setModule($module)
    {
        $this->module = $module;
    }

    public function setComponent($component)
    {
        $this->component = $component;
    }

    public function renderGreen($file)
    {
        $this->f3->set('renderFile', "green/$file");

        $this->f3->set('renderJS', array(
            "https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js",
            "/assets/green/green.js",
            "/assets/green/dataFormEditor.js"
        ));

        $this->f3->set('renderCSS', array(
            "/assets/green/green.css"
        ));

        echo View::instance()->render("layout/$this->layout/layout.php");
    }

    public function renderGreenAjax($file)
    {
        echo View::instance()->render("green/$file");
    }

    public function renderPage()
    {
        $this->f3->set('renderFile', "coreRender/page.php");

        $this->f3->set('renderJS', array(
            "/assets/js/page.js",
            "/assets/js/systemPages/submissionsBoard.js",
            "/assets/js/systemPages/distributor-users.js",
            "/assets/js/systemPages/calendar.js",
            "/assets/js/systemPages/assessment.js",
            "https://cdn.jsdelivr.net/npm/gridstack@0.6.0/dist/gridstack.all.js"
        ));

        $this->f3->set('renderCSS', array(
            "https://cdn.jsdelivr.net/npm/gridstack@0.6.0/dist/gridstack.min.css"
        ));

        $this->f3->set('objUser', $this->objUser);

        $this->f3->set('renderModule', $this->module);
        $this->f3->set('renderComponent', $this->component);

        $this->f3->set('SESSION.layout-page-title', true);

        // LayoutRender::setAsideMenu($this->f3, $this->objUser->typeId);

        echo View::instance()->render("layout/$this->layout/layout.php");
    }

    public function renderExternalPage($page)
    {
        $this->f3->set('renderFile', $page);

        $this->f3->set('renderJS', array());

        $this->f3->set('renderCSS', array());

        $this->f3->set('objUser', $this->objUser);

        $this->f3->set('renderModule', $this->module);
        $this->f3->set('renderComponent', $this->component);

        $this->f3->set('SESSION.layout-page-title', false);

        echo View::instance()->render("layout/external/layout.php");
    }

    public function renderPageContent($type)
    {
        echo View::instance()->render("coreRender/types/$type.php");
    }

    public function renderPageContentCustom($fileName)
    {
        echo View::instance()->render($fileName);
    }

    public function getRenderPageContentCustom($fileName)
    {
        return View::instance()->render($fileName);
    }

    public function renderErrorPage($errorTitle, $errorMessage, $errorDescription)
    {
        $this->f3->set('errorTitle', $errorTitle);
        $this->f3->set('errorMessage', $errorMessage);
        $this->f3->set('errorDescription', $errorDescription);
        echo View::instance()->render("coreRender/error.php");
    }

    public function renderModule()
    {
        $this->f3->set('renderFile', "modules/$this->module/module.php");

        $this->f3->set('renderJS', array(
            "/modules/js/$this->module.js"
        ));

        $this->f3->set('renderCSS', array(
            "/modules/css/$this->module.css"
        ));

        $this->f3->set('objUser', $this->objUser);

        $this->f3->set('renderModule', $this->module);
        $this->f3->set('renderComponent', $this->component);

        LayoutRender::setMainMenu($this->f3, $this->db, $this->objUser->menuId);

        LayoutRender::setupLayout($this->f3, $this->objUser->typeId);

        echo View::instance()->render("layout/$this->layout/layout.php");
    }

    public function renderModuleComponent($codeName)
    {
        $this->f3->set('objUser', $this->objUser);
        echo View::instance()->render("modules/$this->module/components/$codeName.php");
    }

    public static function setupLayout($f3, $dbUser)
    {

        $f3->set('SESSION.layout-headerTopbar-supadminmenuitems', false);
        $f3->set('SESSION.layout-page-title', true);

        $userTypeId = $dbUser->typeId;
        if ($dbUser->typeId == 1 && $dbUser->asTypeId > 0) {
            $userTypeId = $dbUser->asTypeId;
            $f3->set('SESSION.layout-headerTopbar-supadminmenuitems', true);
        }

        switch ($userTypeId) {
            case 1:
                $f3->set('SESSION.layout-headerTopbar-supadminmenuitems', true);
                $f3->set('SESSION.layout-headerMenu', true);
                $f3->set('SESSION.layout-headerTopbar-search', true);
                $f3->set('SESSION.layout-headerTopbar-notifications', true);
                $f3->set('SESSION.layout-headerTopbar-quickActions', false);
                $f3->set('SESSION.layout-headerTopbar-myCart', false);
                $f3->set('SESSION.layout-quickPanel', false);
                $f3->set('SESSION.layout-stickyToolbar', false);
                $f3->set('SESSION.layout-asideScreenPanel', false);
                $f3->set('SESSION.layout-homeChat', false);
                break;

            case 10:
            case 15:
                $f3->set('SESSION.layout-page-title', true);
                $f3->set('SESSION.layout-headerMenu', false);
                $f3->set('SESSION.layout-headerTopbar-search', false);
                $f3->set('SESSION.layout-headerTopbar-notifications', false);
                $f3->set('SESSION.layout-headerTopbar-quickActions', false);
                $f3->set('SESSION.layout-headerTopbar-myCart', false);
                $f3->set('SESSION.layout-quickPanel', false);
                $f3->set('SESSION.layout-stickyToolbar', false);
                $f3->set('SESSION.layout-asideScreenPanel', false);
                $f3->set('SESSION.layout-homeChat', false);
                break;

            default:
                $f3->set('SESSION.layout-page-title', false);
                $f3->set('SESSION.layout-headerMenu', false);
                $f3->set('SESSION.layout-headerTopbar-search', false);
                $f3->set('SESSION.layout-headerTopbar-notifications', true);
                $f3->set('SESSION.layout-headerTopbar-quickActions', false);
                $f3->set('SESSION.layout-headerTopbar-myCart', false);
                $f3->set('SESSION.layout-quickPanel', false);
                $f3->set('SESSION.layout-stickyToolbar', false);
                $f3->set('SESSION.layout-asideScreenPanel', false);
                $f3->set('SESSION.layout-homeChat', false);
                break;
        }
    }

    public static function getMenuById($db, $menuId, $lang, $parentItemId = 0)
    {

        if (!is_numeric($menuId)) {
            return [];
        }
        $dbMenuItems = new BaseModel($db, "vwWebMenuItems");
        $dbMenuItems->title = "title_" . $lang;
        $dbMenuItems->getWhere("menuId = $menuId and parentItemId=$parentItemId");

        $asideMenu = [];
        while (!$dbMenuItems->dry()) {

            switch ($dbMenuItems->type) {
                case "link":
                    $link = [
                        "id" => $dbMenuItems->id,
                        "type" => $dbMenuItems->type,
                        "pageId" => $dbMenuItems->pageId,
                        "title" => $dbMenuItems->title,
                        "link" => $dbMenuItems->link . ($dbMenuItems->filter != null ? "?$dbMenuItems->filter" : ""),
                        "svgIcon" => $dbMenuItems->svgIconId > 0 ? $dbMenuItems->svg : ""
                    ];
                    $asideMenu[] = $link;
                    break;

                case "section":
                    $section = [
                        "id" => $dbMenuItems->id,
                        "type" => $dbMenuItems->type,
                        "title" => $dbMenuItems->title
                    ];
                    $asideMenu[] = $section;
                    break;

                case "menu":
                    $menu = [
                        "id" => $dbMenuItems->id,
                        "type" => $dbMenuItems->type,
                        "title" => $dbMenuItems->title,
                        "link" => $dbMenuItems->link . ($dbMenuItems->filter != null ? "?$dbMenuItems->filter" : ""),
                        "svgIcon" => $dbMenuItems->svgIconId > 0 ? $dbMenuItems->svg : "",
                        "items" => LayoutRender::getMenuById($db, $menuId, $lang, $dbMenuItems->id)
                    ];
                    $asideMenu[] = $menu;
                    break;

                default:
                    break;
            }

            $dbMenuItems->next();
        }

        return $asideMenu;
    }


    public static function getMainMenu($db, $menuId)
    {
        if (!is_numeric($menuId)) {
            return [];
        }

        $arrData = $db->exec("SELECT * FROM vwMenuItems WHERE (`menuId` = ? OR `menuId` = 0) AND `id` <> 50 ORDER BY `order` ASC, `name_ar` ASC", $menuId);
        $newData = LayoutRender::arrangeMenuItems($arrData);

        return $newData;
    }

    public static function arrangeMenuItems($data, $parentItemId = 0)
    {
        $filteredData = [];
        for ($i = 0; $i < count($data); $i++) {
            if ($data[$i]['parentItemId'] == $parentItemId) {

                switch ($data[$i]['type']) {
                    case "link":
                        $link = [
                            "id" => $data[$i]['id'],
                            "type" => $data[$i]['type'],
                            "name_ar" => $data[$i]['name_ar'],
                            "name_fr" => $data[$i]['name_fr'],
                            "name_en" => $data[$i]['name_en'],
                            "url" => $data[$i]['url'],
                            "method" => $data[$i]['method'],
                            "svgIcon" => $data[$i]['svgIcon']
                        ];
                        $filteredData[] = $link;

                        break;

                    case "section":
                        $section = [
                            "id" => $data[$i]['id'],
                            "type" => $data[$i]['type'],
                            "name_ar" => $data[$i]['name_ar'],
                            "name_fr" => $data[$i]['name_fr'],
                            "name_en" => $data[$i]['name_en']
                        ];
                        $filteredData[] = $section;
                        break;

                    case "menu":
                        $menu = [
                            "id" => $data[$i]['id'],
                            "type" => $data[$i]['type'],
                            "name_ar" => $data[$i]['name_ar'],
                            "name_fr" => $data[$i]['name_fr'],
                            "name_en" => $data[$i]['name_en'],
                            "url" => $data[$i]['url'],
                            "method" => $data[$i]['method'],
                            "svgIcon" => $data[$i]['svgIcon'],
                            "items" => LayoutRender::arrangeMenuItems($data, $data[$i]['id'])
                        ];
                        $filteredData[] = $menu;
                        break;

                    default:
                        break;
                }
            }
        }

        return $filteredData;
    }

    public static function setMainMenu($f3, $db, $menuId)
    {

        $menu = LayoutRender::getMainMenu($db, $menuId);
        $f3->set('SESSION.mainMenu', $menu);

        return $menu;
    }

    public static function renderLayoutComponent($componentName)
    {
        echo View::instance()->render("layout/components/$componentName.php");
    }
}
