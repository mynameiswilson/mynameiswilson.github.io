<?php
class Tumblr extends CI_Controller {
        private $tumblr_data;
        public  $tumblr_name;
        private $entries_loaded = false;
        private $per_page = 10;

        public function __construct()
        {
            parent::__construct();
            $this->load->helper('url');

            // handle POST from form
            if ( $this->input->post("tumblr_name") ):

                if ($this->tumblr_name = $this->validate_tumblr_name($this->input->post("tumblr_name"))):
                    redirect("/".$this->tumblr_name);
                else:
                    $this->page($this->input->post("tumblr_name"));
                endif;

            endif;
        }

        // validate_tumblr_name
        // accepts a string, and validates it 
        // tumblr site names are alphanumeric only
        // returns FALSE if not a valid name
        // returns the FULL tumblr URL (example.tumblr.com) if valid
        private function validate_tumblr_name($tumblr_name) {
            $output_array = array();
            preg_match("/^[a-zA-Z\d]+(\.tumblr\.com)?$|(?:[-A-Za-z0-9]+\.)+[A-Za-z]{2,6}/", $tumblr_name, $output_array);
            if (empty($output_array)):
                return false;
            else:
                return $output_array[0] . ( (strpos($output_array[0],".") === FALSE) ? ".tumblr.com" : "");
            endif;
        }

        // page
        // our main controller function (see application/config/routes.php)
        //
        public function page($tumblr_name='',$page_number=1) {
            $this->load->view('tumblrheader');
            if (!empty($tumblr_name)):
            if  ( $this->tumblr_name = $this->validate_tumblr_name($tumblr_name) ):

                    $entries_loaded = $this->fetch_entries($this->tumblr_name,$this->per_page,$this->per_page * ($page_number-1) );
                    if ($entries_loaded):
                        $this->load->library('pagination');

                        //wow this pagination library is NICE but I wish it had a more concise 
                        //way to configure it!
                        $config['base_url'] = "/codetest/codeigniter/".$this->tumblr_name."/"; //$_SERVER['PATH_INFO'];
                        $config['total_rows'] = $this->tumblr_data->response->blog->posts;
                        $config['uri_segment'] = 2;
                        $config['num_links'] = 5;
                        $config['per_page'] = 10;
                        $config['use_page_numbers'] = TRUE;
                        $config['full_tag_open'] = '<ul class="pagination">';
                        $config['full_tag_close'] = '</ul>';
                        $config['first_tag_open'] = '<li>';
                        $config['first_tag_close'] = '</li>';
                        $config['first_link'] = '|&laquo;';
                        $config['last_tag_open'] = '<li>';
                        $config['last_tag_close'] = '</li>';
                        $config['last_link'] = '&raquo;|';
                        $config['next_tag_open'] = '<li>';
                        $config['next_tag_close'] = '</li>';
                        $config['prev_tag_open'] = '<li>';
                        $config['prev_tag_close'] = '</li>';
                        $config['num_tag_open'] = '<li>';
                        $config['num_tag_close'] = '</li>';
                        $config['cur_tag_open'] = '<li class="active"><a href="#">';
                        $config['cur_tag_close'] = '</a></li>';
                        $this->pagination->initialize($config);
                        
                        $this->load->view('tumblrview',$this->tumblr_data);
                    else:
                   
                        $data['message'] = "$this->tumblr_name isn't a valid Tumblr";
                        $this->load->view('tumblrform',$data);

                    endif;

                else:

                    $data['message'] = "$tumblr_name does not appear to be a valid Tumblr site name";
                    $this->tumblr_name = $tumblr_name;
                    $this->load->view('tumblrform',$data);

                endif;
            else:

                $data['message'] = "e.g. oldloves, oldloves.tumblr.com, www.worstroom.com";
                $this->load->view('tumblrform',$data);
            
            endif;

            $this->load->view('tumblrfooter');
        }

        private function fetch_entries($blog,$limit,$offset) {
            if ( isset($blog) ):

                $api_key = 'fuiKNFp9vQFvjLNvx4sUwti4Yb5yGutBN4Xh10LXZhhRKjWlV4';
                $api_url_template = "http://api.tumblr.com/v2/blog/%s/posts?api_key=%s&limit=%d&offset=%d&filter=text";

                $api_url = sprintf($api_url_template,$blog,$api_key,$limit,$offset);

                if (ini_get('allow_url_fopen') == 1):
                    
                    $contents = @file_get_contents($api_url); // @ will supress the PHP warning a 404 will generate
                                                              // we'll handle that true/false later
                
                    $entries = json_decode($contents);
                else:
                    
                    $curl_handle = curl_init();
                    curl_setopt($curl_handle, CURLOPT_URL, $api_url);
                    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);

                    $buffer = curl_exec($curl_handle);
                    curl_close($curl_handle);

                    $entries = json_decode($buffer);

                endif; 

                
                if ($entries):
                    if (isset($entries->meta) && isset($entries->response)):
                        $this->tumblr_data = $entries;
                        return true;
                    else:
                        return false;
                    endif;
                else:
                    return false;
                endif;
            else:
                return false;
            endif;
        }
}
?>
