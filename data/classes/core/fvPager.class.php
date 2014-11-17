<?php

    /**
     * DEPREACTED!!! Use Component_Pager
     */

    class fvPager extends fvDebug implements ArrayAccess, Iterator{
        /**
         * @var fvRootManager
         */
        private $_manager;
        private $_pageCount;
        private $_objects;
        private $_currentPage;
        private $_paramName;
        private $_perPage;

        public function __construct( $manager, $objects = null, $paramName = "page", $perPage = null ){
            $this->_manager = $manager;
            $this->_objects = $objects;
            $this->_paramName = $paramName;

            if( !( $this->_perPage = $perPage ) ){
                if( !( $this->_perPage = fvSite::$fvConfig->get( "modules." . fvRoute::getInstance()
                    ->getModuleName() . ".pager.show_per_page" ) )
                ){
                    $this->_perPage = fvSite::$fvConfig->get( "pager.show_per_page" );
                }
            }
        }

        public function showPagerAdmin( $full = false ){
            $content = "";
            $separator = false;
            for( $i = 0; $i < $this->_pageCount; $i++ ){
                if( $full || $this->_checkPage( $i ) ){
                    $separator = false;
                    $href = fvRequest::getInstance()
                        ->parseQueryString( $_SERVER['REQUEST_URI'], $this->_paramName, $i );
                    if( $i == $this->_currentPage )
                        $content .= "<span>" . ( $i + 1 ) . "</span>";
                    else $content .= "<a href='$href'>" . ( $i + 1 ) . "</a>";
                }
                else{
                    if( !$separator ){
                        $content .= "<span class='sep'>...</span>";
                        $separator = true;
                    }
                }
            }

            return $content;
        }

        function paginateQuery( fvQuery $fvQuery ){
            $this->_currentPage = fvRequest::getInstance()->getRequestParameter( $this->_paramName, 'int' );
            $count = (int)$fvQuery->getCount();
            $this->_pageCount = ceil( $count / $this->_perPage );

            $fvQuery->limit( (int)$this->_perPage, (int)( $this->_currentPage * $this->_perPage ) );
            $this->_objects = $this->_manager->getAllByQuery( $fvQuery );
            return $this;
        }

        public function paginate( $where = null, $order = null, $params = array(), fvQuery $fvQuery = null ){
            $this->_currentPage = fvRequest::getInstance()->getRequestParameter( $this->_paramName, 'int' );

            if( !is_null( $this->_objects ) ){
                $count = count( $this->_objects );
            }
            else{
                if( $fvQuery == null ){
                    $count = $this->_manager->getCount( $where, $params );
                }
                else{
                    $count = (int)$fvQuery->getCount();
                }
            }

            $this->_pageCount = ceil( $count / $this->_perPage );

            if( !is_null( $this->_objects ) ){
                $this->_objects = array_slice( $this->_objects,
                                               $this->_currentPage * $this->_perPage,
                                               $this->_perPage,
                                               true );
            }
            else{
                if( $fvQuery !== null ){
                    $fvQuery->limit( (int)( $this->_currentPage * $this->_perPage ), $this->_perPage );
                    $this->_objects = $this->_manager->getAllByQuery( $fvQuery );
                }
                else{
                    $this->_objects = $this->_manager->getAll( $where,
                                                               $order,
                                                               ( (int)( $this->_currentPage * $this->_perPage ) ) . "," . $this->_perPage,
                                                               $params );
                }
            }
            //        var_dump($this->_rows);
            return $this;
        }

        public function getData(){
            return $this->_objects;
        }

        public function hasPaginate(){
            return ( $this->_pageCount > 1 );
        }

        public function paginateSQL( $sql, $objPerPage = null, $addField = array() ){
            if( !$objPerPage )
                $objPerPage = $this->_perPage;

            $this->_currentPage = fvRequest::getInstance()->getRequestParameter( $this->_paramName, 'int' );

            $expSQL = explode( " from ", $sql );
            $expSQL = "select count(*) as 'ct' from " . $expSQL[1];

            $count = fvSite::$pdo->getAssoc( $expSQL );
            $count = $count[0]['ct'];
            $this->_pageCount = ceil( $count / $objPerPage );

            $this->_objects = $this->_manager->getObjectBySQL( $sql . " limit " . ( (int)( $this->_currentPage * $objPerPage ) ) . "," . $objPerPage,
                                                               $addField );
            //$this->debug($this->_rows);

            return $this;
        }

        public function paginateGroupSQL( $sql, $objPerPage = null, $addField = array() ){
            if( !$objPerPage )
                $objPerPage = $this->_perPage;
            $this->_currentPage = fvRequest::getInstance()->getRequestParameter( $this->_paramName, 'int' );

            $sql = strtolower( $sql );
            $sql = str_replace( "select", "select SQL_CALC_FOUND_ROWS ", $sql );
            $sql = $sql . " limit " . ( (int)( $this->_currentPage * $objPerPage ) ) . "," . $objPerPage;

            $this->_objects = $this->_manager->getObjectBySQL( $sql, $addField );

            $expSQL = " SELECT FOUND_ROWS() as 'ct' ; ";
            $count = fvSite::$pdo->getAssoc( $expSQL );

            $count = $count[0]['ct'];
            $this->_pageCount = ceil( $count / $objPerPage );


            //$this->debug($this->_rows);

            return $this;
        }

        protected function _checkPage( $pageNum ){
            if( $this->_pageCount < 10 )
                return true;
            if( ( $pageNum < 3 ) || ( ( $this->_pageCount - $pageNum ) < 4 ) )
                return true;
            if( abs( $pageNum - $this->_currentPage ) < 3 )
                return true;

            return false;
        }

        public function getPageHref( $page ){
            return fvRequest::getInstance()->parseQueryString( $_SERVER['REQUEST_URI'], $this->_paramName, $page );
        }

        public function showPager( $full = false ){
            $content = "";
            $separator = false;
            for( $i = 0; $i < $this->_pageCount; $i++ ){
                if( $full || $this->_checkPage( $i ) ){
                    $separator = false;
                    if( $i == $this->_currentPage )
                        $content .= "<span class='active'>" . ( $i + 1 ) . "</span>";
                    else $content .= "<a href='{$this->getPageHref($i)}'>" . ( $i + 1 ) . "</a>";
                }
                else{
                    if( !$separator ){
                        $content .= "<span class='sep'>...</span>";
                        $separator = true;
                    }
                }
            }

            return $content;
        }

        public function showPagerRoute( $full = false, $position ){
            $content = "";
            $separator = false;
            for( $i = 0; $i < $this->_pageCount; $i++ ){
                if( $full || $this->_checkPage( $i ) ){
                    $separator = false;
                    $href = fvRequest::getInstance()
                        ->parseQueryString( $_SERVER['REQUEST_URI'], $this->_paramName, $i, $position );
                    if( $i == $this->_currentPage )
                        $content .= "<span>" . ( $i + 1 ) . "</span>";
                    else $content .= "<a href='$href'>" . ( $i + 1 ) . "</a>";
                }
                else{
                    if( !$separator ){
                        $content .= "<span class='sep'>...</span>";
                        $separator = true;
                    }
                }
            }

            return $content;
        }

        public function showPagerAjax( $full = true, $userfunck = "" ){
            $content = "";
            $separator = false;
            //        var_dump($this->_pageCount);
            for( $i = 0; $i < $this->_pageCount; $i++ ){
                if( $full || $this->_checkPage( $i ) ){
                    $separator = false;
                    $href = fvRequest::getInstance()
                        ->parseQueryString( $_SERVER['REQUEST_URI'], $this->_paramName, $i );
                    if( $i == $this->_currentPage )
                        $content .= "<span class='act'>" . ( $i + 1 ) . "</span>";
                    else $content .= "<a href=\"javascript:void(0);\" onclick=\"$userfunck(" . $i . ")\">" . ( $i + 1 ) . "</a>";
                }
                else{
                    if( !$separator ){
                        $content .= "<span class='sep'>...</span>";
                        $separator = true;
                    }
                }
            }

            return $content;
        }

        public function getCurrentPage(){
            return $this->_currentPage;
        }

        public function getPageCount(){
            return $this->_pageCount;
        }

        function offsetExists( $offset ){
            return isset( $this->_objects[$offset] );
        }

        function offsetGet( $offset ){
            return $this->_objects[$offset];
        }

        function offsetUnset( $offset ){
            unset( $this->_objects[$offset] );
        }

        function offsetSet( $offset, $newValue ){
            $this->_objects[$offset] = $newValue;
        }

        public function rewind(){
            reset( $this->_objects );
        }

        public function current(){
            $var = current( $this->_objects );
            return $var;
        }

        public function key(){
            $var = key( $this->_objects );
            return $var;
        }

        public function next(){
            $var = next( $this->_objects );
            return $var;
        }

        public function valid(){
            $var = $this->current() !== false;
            return $var;
        }

        public function getObjects(){
            return $this->_objects;
        }

        public function getPerPage(){
            return $this->_perPage;
        }

        public function showAjaxPager( $full = true, $userfunck = "" ){
            $content = "";
            $separator = false;
            for( $i = 0; $i < $this->_pageCount; $i++ ){
                if( $full || $this->_checkPage( $i ) ){
                    $separator = false;
                    $href = fvRequest::getInstance()
                        ->parseQueryString( $_SERVER['REQUEST_URI'], $this->_paramName, $i );
                    if( $i == $this->_currentPage )
                        $content .= "<span>" . ( $i + 1 ) . "</span>";
                    else $content .= "<a href='javascript:void(0);' onclick='$userfunck(" . $i . ")'>" . ( $i + 1 ) . "</a>";
                }
                else{
                    if( !$separator ){
                        $content .= "<span class='sep'>...</span>";
                        $separator = true;
                    }
                }
            }

            return $content;
        }

        public function getEntity(){
            return $this->_manager->getEntity();
        }

    }


