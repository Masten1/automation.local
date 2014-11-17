<?php

class MenuModule extends fvModule {

    function __construct () 
    {
        $this->moduleName = strtolower(substr(__CLASS__, 0, -6));
        parent::__construct(fvSite::$fvConfig->get("modules.{$this->moduleName}.smarty.template"), 
        fvSite::$fvConfig->get("modules.{$this->moduleName}.smarty.compile"), 
        fvSite::$Layoult);
    }
 
    function showMainMenu() {
        $modules = fvSite::$fvConfig->get("modules");
                
        $this->currentModuleTree = array();
            
        foreach ($modules as $key => $module) {
            
            if (!$module['access']['enable'] || $this->current_page->getLoggedUser()->check_acl($module['access']['acl'])) 
            {
                if (strlen(trim($module['menu_path'])) > 0) 
                {
                    if (count($module_path = explode('/', $module['menu_path'])) < 2) 
                    {
                        $module_path[0] = 'другое';
                        $module_path[1] = $module['menu_path'];
                    }
            
                    $this->currentModuleTree[md5($module_path[0])]['name'] = $module_path[0];
                    $this->currentModuleTree[md5($module_path[0])]['child_nodes'][] = array(
                        'name'          => $module_path[1],
                        'image_name'    => $module['icon'],
                        'href'          => fvSite::$fvConfig->get("dir_web_root") . "$key/",
                    );
                }
            }
        }

        @uasort($this->currentModuleTree, array($this, '_cmpModules'));
            
        $this->__assign('currentModuleTree', $this->currentModuleTree);
        return $this->__display('menu.tpl');
    }
    
    private function _find_in_array ($a, $array) {
        foreach ($array as $key => $value) {
            if ($value === $a) return $key;
        }
        return false;
    }
        
    private function _cmpModules($a, $b) {
        if (is_array($a['child_nodes'])) {
            
            if ($key = $this->_find_in_array($a, $this->currentModuleTree))
                @uasort($this->currentModuleTree[$key]['child_nodes'], array($this, '_cmpModules'));
        }
        if (is_array($b['child_nodes'])) {
            if ($key = $this->_find_in_array($b, $this->currentModuleTree))
                @uasort($this->currentModuleTree[$key]['child_nodes'], array($this, '_cmpModules'));
        }
        
        if ($a['name'] == $b['name']) {
            return 0;
        }
        return ($a['name'] < $b['name']) ? -1 : 1;
    }   
}

?>
