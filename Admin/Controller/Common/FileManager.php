<?php
namespace Admin\Controller\Common;

use Libs\Core\ControllerAdmin AS Controller;
use Libs\Core\Loader as L;
use Libs\ExtendsClass\Common as C;
use Libs\ExtendsClass\Image;
use Libs\ExtendsClass\Pagination;

class FileManager extends Controller {
	public function index() {
        include_once ROOT_PATH . 'Libs' . DIRECTORY_SEPARATOR . 'ExtendsClass' . DIRECTORY_SEPARATOR . 'Utf8.php';
		// Find which protocol to use to pass the full image link back
        $server = HTTP_SERVER;

        $c_filter_name = C::hsc($_GET['filter_name']);

		if (isset($c_filter_name)) {
			$filter_name = rtrim(str_replace('*', '', $c_filter_name), '/');
		} else {
			$filter_name = null;
		}

        $c_directory = C::hsc($_GET['directory']);
		// Make sure we have the correct directory
		if (isset($c_directory)) {
			$directory = rtrim(DIR_IMAGE . 'catalog/' . str_replace('*', '', $c_directory), '/');
			setcookie('imagemanager_last_open_folder', $directory, time() + 60 * 60 * 24 * 30 * 24, '/', $_SERVER['HTTP_HOST']);
		} else {
			$directory = DIR_IMAGE . 'catalog';
		}

        $c_page = C::hsc($_GET['page']);
		if (isset($c_page)) {
			$page = $c_page;
		} else {
			$page = 1;
		}

		$directories = [];
		$files = [];

		$data['images'] = [];

//		if (substr(str_replace('\\', '/', realpath($directory . '/' . $filter_name)), 0, strlen(DIR_IMAGE . 'catalog')) == DIR_IMAGE . 'catalog') {
//
//		}

        // Get directories
        $directories = glob($directory . '/' . $filter_name . '*', GLOB_ONLYDIR);

        if (!$directories) {
            $directories = [];
        }

        // Get files
        $files = glob($directory . '/' . $filter_name . '*.{jpg,jpeg,png,gif,JPG,JPEG,PNG,GIF}', GLOB_BRACE);

        if (!$files) {
            $files = [];
        }

		// Merge directories and files
		$images = array_merge($directories, $files);

		// Get total number of files and directories
		$image_total = count($images);

		// Split the array based on current page number and max number of items per page of 10
		$images = array_splice($images, ($page - 1) * 16, 16);

        $image_tools = new Image();

        $c_target = C::hsc($_GET['target']);
        $c_thumb = C::hsc($_GET['thumb']);
		foreach ($images as $image) {
			$name = str_split(basename($image), 14);

			if (is_dir($image)) {
				$url = '';

				if (isset($c_target)) {
					$url .= '&target=' . $c_target;
				}

				if (isset($c_thumb)) {
					$url .= '&thumb=' . $c_thumb;
				}

				$data['images'][] = array(
					'thumb' => '',
					'name'  => implode(' ', $name),
					'type'  => 'directory',
					'path'  => utf8_substr($image, utf8_strlen(DIR_IMAGE)),
					'href'  => "{$this->data['entrance']}route=Admin/Common/FileManager"  .'&directory=' . urlencode(utf8_substr($image, utf8_strlen(DIR_IMAGE . 'catalog/'))) . $url
				);
			} elseif (is_file($image)) {
				$data['images'][] = array(
					'thumb' =>$image_tools->resize(utf8_substr($image, utf8_strlen(DIR_IMAGE)), URL_IMAGE_CACHE_WITH, URL_IMAGE_CACHE_HEIGHT),
					'name'  => implode(' ', $name),
					'type'  => 'image',
					'path'  => utf8_substr($image, utf8_strlen(DIR_IMAGE)),
					'href'  => $server . 'image/' . utf8_substr($image, utf8_strlen(DIR_IMAGE))
				);
			}
		}

		$data['heading_title']      = '图像管理器';

		$data['text_no_results']    = '没有符合条件的结果！';
		$data['text_confirm']       = '确定吗？';

		$data['entry_search']       = '检索中.....';
		$data['entry_folder']       = '文件夹名称';

		$data['button_parent']      = '上一级';
		$data['button_refresh']     = '刷新';;
		$data['button_upload']      = '上传';
		$data['button_folder']      = '新目录';
		$data['button_delete']      = '删除';
		$data['button_search']      = '检索';

		if (isset($c_directory)) {
			$data['directory'] = urlencode($c_directory);
		} else {
			$data['directory'] = '';
		}

		if (isset($c_filter_name)) {
			$data['filter_name'] = $c_filter_name;
		} else {
			$data['filter_name'] = '';
		}

		// Return the target ID for the file manager to set the value
		if (isset($c_target)) {
			$data['target'] = $c_target;
		} else {
			$data['target'] = '';
		}

		// Return the thumbnail for the file manager to show a thumbnail
		if (isset($c_thumb)) {
			$data['thumb'] = $c_thumb;
		} else {
			$data['thumb'] = '';
		}

		// Parent
		$url = '';

		if (isset($c_directory)) {
			$pos = strrpos($c_directory, '/');

			if ($pos) {
				$url .= '&directory=' . urlencode(substr($c_directory, 0, $pos));
			}
		}

		if (isset($c_target)) {
			$url .= '&target=' . $c_target;
		}

		if (isset($c_thumb)) {
			$url .= '&thumb=' . $c_thumb;
		}

		$data['parent'] =  "{$this->data['entrance']}route=Admin/Common/FileManager" . $url;

		// Refresh
		$url = '';

		if (isset($c_directory)) {
			$url .= '&directory=' . urlencode($c_directory);
		}

		if (isset($c_target)) {
			$url .= '&target=' . $c_target;
		}

		if (isset($c_thumb)) {
			$url .= '&thumb=' . $c_thumb;
		}

		$data['refresh'] = "{$this->data['entrance']}route=Admin/Common/FileManager" . $url;

		$url = '';

		if (isset($c_directory)) {
			$url .= '&directory=' . urlencode(html_entity_decode($c_directory, ENT_QUOTES, 'UTF-8'));
		}

		if (isset($c_filter_name)) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($c_filter_name, ENT_QUOTES, 'UTF-8'));
		}

		if (isset($c_target)) {
			$url .= '&target=' . $c_target;
		}

		if (isset($c_thumb)) {
			$url .= '&thumb=' . $c_thumb;
		}

		$pagination = new Pagination();
		$pagination->total = $image_total;
		$pagination->page = $page;
		$pagination->limit = 16;
		$pagination->url = "{$this->data['entrance']}route=Admin/Common/FileManager" . $url . '&page={page}';

        $data['pagination'] = $pagination->render();

        $this->data = array_merge($this->data , $data);

        L::output(L::view('Common\\FileManager', 'Admin', $this->data));
	}

	public function upload() {
        // Check user has permission
//		if (!$this->user->hasPermission('modify', 'common/filemanager')) {
//			$json['error'] = $this->language->get('error_permission');
//		}

        include_once ROOT_PATH . 'Libs' . DIRECTORY_SEPARATOR . 'ExtendsClass' . DIRECTORY_SEPARATOR . 'Utf8.php';

        $error_message['error_upload_1']                = '警告:上传文档大小超过了php.ini中设置的最大值！';
        $error_message['error_upload_2']                = '警告: 上传文档大小超过了HTML格式中规定的 MAX_FILE_SIZE !';
        $error_message['error_upload_3']                = '警告: 仅部分文档被上传！';
        $error_message['error_upload_4']                = '警告: 未上传任何文件！';
        $error_message['error_upload_6']                = '警告: 缺少临时文件目录！';
        $error_message['error_upload_7']                = '警告: 写文件失败！';
        $error_message['error_upload_8']                = '警告: 扩展终止了文件上传！';
        $error_message['error_upload_999']              = '警告: 无可用错误代码！';

		$json = [];

        $c_directory = C::hsc($_GET['directory']);
		// Make sure we have the correct directory
		if (isset($c_directory)) {
			$directory = rtrim(DIR_IMAGE . 'catalog/' . $c_directory, '/');
		} else {
			$directory = DIR_IMAGE . 'catalog';
		}

		// Check its a directory
		if (!is_dir($directory)) {
			$json['error'] = '警告: 目录不存在！';
		}

		if (!$json) {
			// Check if multiple files are uploaded or just one
			$files = [];

			if (!empty($_FILES['file']['name']) && is_array($_FILES['file']['name'])) {
				foreach (array_keys($_FILES['file']['name']) as $key) {
					$files[] = [
						'name'     => $_FILES['file']['name'][$key],
						'type'     => $_FILES['file']['type'][$key],
						'tmp_name' => $_FILES['file']['tmp_name'][$key],
						'error'    => $_FILES['file']['error'][$key],
						'size'     => $_FILES['file']['size'][$key]
                    ];
				}
				
				//var_dump($this->request->files['file']);exit;
			}

			foreach ($files as $file) {
				if (is_file($file['tmp_name'])) {
					// Sanitize the filename
					$filename = basename(html_entity_decode($file['name'], ENT_QUOTES, 'UTF-8'));

					// Validate the filename length
					if ((utf8_strlen($filename) < 3) || (utf8_strlen($filename) > 255)) {
						$json['error'] = '警告: 文件名称必须介于3 - 255字符之间！';
					}
					
					// Allowed file extension types
					$allowed = [
						'jpg',
						'jpeg',
						'gif',
						'png'
                    ];
	
					if (!in_array(utf8_strtolower(utf8_substr(strrchr($filename, '.'), 1)), $allowed)) {
						$json['error'] = '警告: 不正确的文件类型！';
					}
					
					// Allowed file mime types
					$allowed = [
						'image/jpeg',
						'image/pjpeg',
						'image/png',
						'image/x-png',
						'image/gif'
                    ];
	
					if (!in_array($file['type'], $allowed)) {
						$json['error'] = '警告: 不正确的文件类型！';
					}

					// Return any upload error
					if ($file['error'] != UPLOAD_ERR_OK) {
						$json['error'] = $error_message['error_upload_' . $file['error']];
					}
					
					if (intval($file['size']) > 1024000) {
						$json['error'] = '警告: 不正确的文件大小！图片文件大小不能大于 1M , 图片宽与高均不能大于2000px';
					}
					
					if (!isset($json['error'])) {
						list($width_orig, $height_orig, $image_type) = getimagesize($file['tmp_name']);
						
						if ((intval($width_orig) > 2000) || (intval($height_orig) > 2000)) {
							$json['error'] = '警告: 不正确的文件大小！图片文件大小不能大于 1M , 图片宽与高均不能大于2000px';
						}
					}

				} else {
					$json['error'] = '警告: 未知原因，无法上传文件！';
				}

				if (!$json) {
					move_uploaded_file($file['tmp_name'], $directory . '/' . $filename);
				}
			}
		}

		if (!$json) {
			$json['success'] = '成功: 文件已经上传！';
		}

        exit(json_encode($json, JSON_UNESCAPED_UNICODE));
//        header('Content-Type: application/json', true);
//		$this->response->addHeader('Content-Type: application/json');
//		$this->response->setOutput(json_encode($json));
	}

	public function folder() {
        // Check user has permission
//        if (!$this->user->hasPermission('modify', 'common/filemanager')) {
//            $json['error'] = $this->language->get('error_permission');
//        }

        include_once ROOT_PATH . 'Libs' . DIRECTORY_SEPARATOR . 'ExtendsClass' . DIRECTORY_SEPARATOR . 'Utf8.php';

		$json = [];

        $c_directory = C::hsc($_GET['directory']);

		// Make sure we have the correct directory
		if (isset($c_directory)) {
			$directory = rtrim(DIR_IMAGE . 'catalog/' . $c_directory, '/');
		} else {
			$directory = DIR_IMAGE . 'catalog';
		}

		// Check its a directory
		if (!is_dir($directory)) {
			$json['error'] = '警告: 目录不存在！';
		}

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			// Sanitize the folder name
			$folder = basename(html_entity_decode(c::hsc($_POST['folder']), ENT_QUOTES, 'UTF-8'));

			// Validate the filename length
			if ((utf8_strlen($folder) < 3) || (utf8_strlen($folder) > 128)) {
				$json['error'] = '警告: 文件夹名称必须介于3到255字符之间！';
			}

			// Check if directory already exists or not
			if (is_dir($directory . '/' . $folder)) {
				$json['error'] = '警告: 已经存在同名文件夹或文件！';
			}
		}

		if (!isset($json['error'])) {
			mkdir($directory . '/' . $folder, 0777);
			chmod($directory . '/' . $folder, 0777);

			@touch($directory . '/' . $folder . '/' . 'index.html');

			$json['success'] = '成功: 目录已经创建！';
		}

        exit(json_encode($json, JSON_UNESCAPED_UNICODE));
//		$this->response->addHeader('Content-Type: application/json');
//		$this->response->setOutput(json_encode($json));
	}

	public function delete() {
        // Check user has permission
//        if (!$this->user->hasPermission('modify', 'common/filemanager')) {
//            $json['error'] = $this->language->get('error_permission');
//        }

		$json = [];
        $c_path = C::hsc($_POST['path']);

		if (isset($c_path)) {
			$paths = $c_path;
		} else {
			$paths = [];
		}

		// Loop through each path to run validations
        if (!empty($paths)) {
            foreach ($paths as $path) {
                // Check path exsists
                #if ($path == DIR_IMAGE . 'catalog' || substr(str_replace('\\', '/', realpath(DIR_IMAGE . $path)), 0, strlen(DIR_IMAGE . 'catalog')) != DIR_IMAGE . 'catalog') {
                if ($path == DIR_IMAGE . 'catalog') {
                    $json['error'] = '警告: 不能删除此目录！';

                    break;
                }
            }
        }

		if (!$json and !empty($paths)) {
			// Loop through each path
			foreach ($paths as $path) {
				$path = rtrim(DIR_IMAGE . $path, '/');

				// If path is just a file delete it
				if (is_file($path)) {
					unlink($path);

				// If path is a directory beging deleting each file and sub folder
				} elseif (is_dir($path)) {
					$files = [];

					// Make path into an array
					$path = [$path . '*'];

					// While the path array is still populated keep looping through
					while (count($path) != 0) {
						$next = array_shift($path);

						foreach (glob($next) as $file) {
							// If directory add to path array
							if (is_dir($file)) {
								$path[] = $file . '/*';
							}

							// Add the file to the files to be deleted array
							$files[] = $file;
						}
					}

					// Reverse sort the file array
					rsort($files);

					foreach ($files as $file) {
						// If file just delete
						if (is_file($file)) {
							unlink($file);

						// If directory use the remove directory function
						} elseif (is_dir($file)) {
							rmdir($file);
						}
					}
				}
			}

			$json['success'] = '成功: 文件或目录已经被删除！';
		}

        exit(json_encode($json, JSON_UNESCAPED_UNICODE));
//		$this->response->addHeader('Content-Type: application/json');
//		$this->response->setOutput(json_encode($json));
	}
}
