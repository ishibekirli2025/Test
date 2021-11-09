<?php


class MY_Lang extends MX_Lang  {

    var $language = [];
    var $is_loaded = [];
    var $idiom;
    var $set;

    var $line;
    var $CI;

    function __construct()
    {
        parent::__construct();
    }

    /**
     * Load a language file
     *
     * @access    public
     * @param    mixed    the name of the language file to be loaded. Can be an array
     * @param    string    the language (az, etc.)
     * @return    mixed
     */


    function load($langfile = '', $idiom = '', $return = FALSE, $add_suffix = true, $alt_path = '', $_module = '') {
        // Calling early before CI reformats them
        $this->set = $langfile;
        $this->idiom = $idiom;

        $langfile = str_replace(EXT, '', str_replace('_lang.', '', $langfile)).'_lang'.EXT;

        if (in_array($langfile, $this->is_loaded, TRUE))
        {
            return;
        }

        if ($idiom == '')
        {
            $CI =& get_instance();
            $deft_lang = $CI->config->item('language');
            $idiom = ($deft_lang == '') ? 'az' : $deft_lang;

            $this->idiom = $idiom;
        }

        // Determine where the language file is and load it
        if (file_exists(APPPATH.'language/'.$idiom.'/'.$langfile))
        {
            include(APPPATH.'language/'.$idiom.'/'.$langfile);
        }
        else
        {
            if (file_exists(BASEPATH.'language/'.$idiom.'/'.$langfile))
            {
                include(BASEPATH.'language/'.$idiom.'/'.$langfile);
            }
            else
            {
                $database_lang =  $this->_get_from_db();
                if ( ! empty( $database_lang ) )
                {
                    $lang = $database_lang;
                }else{
                    show_error('Unable to load the requested language file: language/'.$langfile);
                }
            }
        }

        if ( ! isset($lang))
        {
            log_message('error', 'Language file contains no data: language/'.$idiom.'/'.$langfile);
            return;
        }

        if ($return == TRUE)
        {
            return $lang;
        }

        $this->is_loaded[] = $langfile;
        $this->language = array_merge($this->language, $lang);
        unset($lang);

        log_message('debug', 'Language file loaded: language/'.$idiom.'/'.$langfile);
        return TRUE;
    }

    /**
     * Load a language from database
     *
     * @access    private
     * @return    array
     */
    private function _get_from_db()
    {
          $CI =& get_instance();

        $CI->db->select('*');
        $CI->db->from($CI->config->item("language_db_table_name"));
        $CI->db->where($CI->config->item("language_db_table_name"), $this->idiom);
        $CI->db->where('set', $this->set);

        $query = $CI->db->get()->result();

        $return = [];

        foreach ( $query as $row )
        {
            $return[$row->key] = $row->text;
        }

        unset($CI, $query);
        return $return;
    }
}
