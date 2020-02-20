<?php

namespace Application\Models\Factories;

use Application\Models\Entities\Company\Media;
use Application\Models\Entities\Company\News;
use Application\Models\Entities\Company\Person;
use Application\Models\Entities\Company\Course;
use Application\Models\Entities\Company\Chat;
use Application\Models\Entities\Company\MapMarker;
use Application\Models\Entities\Company\Map;
use Application\Models\Entities\Company\Company;
use Application\Models\Entities\Company\Seo;
use Application\Models\Entities\Company\CompanyRoot;
use Illuminate\Support\Facades\Config;
use Application\Models\Entities\Company\CloudLogin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Application\Models\Gateways\Encrypt;

/**
 * Factory for managing Assets - Used by AdminAssetsController
 */
class AssetFactory {
	/**
	 * Function exposed for loading objects
	 * @param string $asset_type
	 * @param integer $id
	 * return Object
	 */
	public static function load($inputs) {
		if (!isset($inputs['id'])) {
			return self::loadByType($inputs);
		} else {
			return self::loadByID($inputs['type'], $inputs['id']);
		}
	}

	/**
	 * Function exposed for saving objects
	 * @param string $asset_type
	 * @param integer $id
	 * @param array $attributes
	 * return Object
	 */
	public static function save($id, $attributes) {
		$type = $attributes['type'];
		if (
			$type == 'media' || 
			$type == 'document' || 
			$type == 'image' || 
			$type == 'video'
		) {
			$asset = Media::find($id);
			$asset->title = $attributes['title'];
			$asset->source = $attributes['source'];
			$asset->thumbnail = $attributes['thumbnail'];
			$asset->type = $attributes['type'];
			$asset->featured = (isset($attributes['featured'])) ? 1 : 0;
			$asset->active = (isset($attributes['active'])) ? 1 : 0;
			return $asset->save();
		} else if ($type == 'news') {
			$asset = News::find($id);
			$asset->title = $attributes['title'];
			$asset->image_url = $attributes['image_url'];
			$asset->short_description = $attributes['short_description'];
			$asset->short_title = $attributes['short_title'];
			$asset->thumbnail = $attributes['thumbnail'];
			$asset->featured = (isset($attributes['featured'])) ? 1 : 0;
			$asset->active = (isset($attributes['active'])) ? 1 : 0;
			$asset->content = $attributes['content'];
			return $asset->save();
		} else if ($type == 'people') {
			$asset = Person::find($id);
			$asset->name = $attributes['name'];
			$asset->avatar_url = $attributes['avatar_url'];
			$asset->thumbnail = $attributes['thumbnail'];
			$asset->short_description = $attributes['short_description'];
			$asset->long_description = $attributes['long_description'];
			$asset->position = $attributes['position'];
			$asset->active = (isset($attributes['active'])) ? 1 : 0;
			return $asset->save();
		} else if ($type == 'course') {
			$asset = Course::find($id);
			$asset->title = $attributes['title'];
			$asset->summary = $attributes['summary'];
			$asset->description = $attributes['description'];
			$asset->apply_online = $attributes['apply_online'];
			$asset->email = $attributes['email'];
			$asset->active = (isset($attributes['active'])) ? 1 : 0;
			$asset->categories()->sync($attributes['category_ids']);
			return $asset->save();
		} else if ($type == 'topic') {
			$asset = Chat::find($id);
			$asset->content = $attributes['content'];
			$asset->active = (isset($attributes['active'])) ? 1 : 0;
			return $asset->save();
		} else if ($type == 'seo') {
			$asset = Seo::find($id);
			$asset->page_title = $attributes['page_title'];
			$asset->meta_title = $attributes['meta_title'];
			$asset->meta_description = $attributes['meta_description'];
			$asset->canonical_url = $attributes['canonical_url'];
			return $asset->save();
		} else if ($type == 'recruit') {
			$asset = CloudLogin::find($id);
			$asset->xcode = $attributes['xcode'];
			$asset->cloud_user_name = $attributes['cloud_user_name'];
			$asset->cloud_password = Encrypt::encryptString($attributes['cloud_password']);
			$asset->user_name = $attributes['user_name'];
			if ($attributes['password'] != '') {
				$asset->password = Hash::make($attributes['password']);
			}
			$asset->cat = $attributes['cat'];
			$asset->board_id = $attributes['board_id'];
			$asset->channel_id = $attributes['channel_id'];
			$asset->upload_frequency = $attributes['upload_frequency'];
			$asset->status = $attributes['status'];
			$asset->notes = $attributes['notes'];
			$asset->ftp_location = self::createFTPPath($attributes['xcode']);
			$asset->created_by = $attributes['user_id'];
			return $asset->save();
		} else if ($type == 'slug') {
			$company_root_id = self::getCompanyRootIdbyCompanyId(
				$attributes['company_id']
			);
			CompanyRoot::where('id',$company_root_id)->update(array("slug"=> $attributes['slug']));
			Company::where('company_root_id',$company_root_id)->where("is_published","1")->update(array("slug" => $attributes['slug']));
			return true;
		} else if ($type == 'map') {
			$asset = Map::find($id);
			if (empty($attributes['markers'])) {
				$asset->delete();
				return true;
			}
			$asset->lat = $attributes['company_lat'];
			$asset->lng = $attributes['company_lng'];
			$asset->zoom = 15;
			$asset->embed_html = $attributes['companyEmbedURL'];
            $asset->map_url = $attributes['map_url'];
			$asset->save();
            $asset->markers()->delete();
            foreach ($attributes['markers'] as $marker) {
                $marker = new MapMarker($marker);
                $asset->markers()->save($marker);
            }
			return true;
		}
	}

	/**
	 * Function exposed for making object
	 * @param string $asset_type
	 * @param array $attributes
	 * return Object
	 */
	public static function make($attributes) {
		$type = $attributes['type'];
		if (
			$type == 'media' || 
			$type == 'document' ||
			$type == 'image' || 
			$type == 'video'
		) {
			$asset = new Media;
			$asset->title = $attributes['title'];
			$asset->source = $attributes['source'];
			$asset->thumbnail = $attributes['thumbnail'];
			$asset->type = $attributes['type'];
			$asset->featured = (isset($attributes['featured'])) ? 1 : 0;
			$asset->active = (isset($attributes['active'])) ? 1 : 0;
			$asset->company_id = $attributes['company_id'];
			$asset->save();
		} else if ($type == 'news') {
			$asset = new News;
			$asset->title = $attributes['title'];
			$asset->image_url = $attributes['image_url'];
			$asset->short_description = $attributes['short_description'];
			$asset->short_title = $attributes['short_title'];
			$asset->thumbnail = $attributes['thumbnail'];
			$asset->featured = (isset($attributes['featured'])) ? 1 : 0;
			$asset->active = (isset($attributes['active'])) ? 1 : 0;
			$asset->content = $attributes['content'];
			$asset->company_id = $attributes['company_id'];
			$asset->save();
		} else if ($type == 'people') {
			$asset = new Person;
			$asset->name = $attributes['name'];
			$asset->avatar_url = $attributes['avatar_url'];
			$asset->thumbnail = $attributes['thumbnail'];
			$asset->short_description = $attributes['short_description'];
			$asset->long_description = $attributes['long_description'];
			$asset->position = $attributes['position'];
			$asset->active = (isset($attributes['active'])) ? 1 : 0;
			$asset->company_id = $attributes['company_id'];
			$asset->save();
		} else if ($type == 'course') {
			$company_root_id = self::getCompanyRootIdbyCompanyId(
				$attributes['company_id']
			);
			$asset = new Course;
			$asset->title = $attributes['title'];
			$asset->company_root_id = $company_root_id;
			$asset->summary = $attributes['summary'];
			$asset->description = $attributes['description'];
			$asset->apply_online = $attributes['apply_online'];
			$asset->email = $attributes['email'];
			$asset->active = (isset($attributes['active'])) ? 1 : 0;
			$asset->save();
			$asset->categories()->sync($attributes['category_ids']);
		} else if ($type == 'topic') {
			$company_root_id = self::getCompanyRootIdbyCompanyId(
				$attributes['company_id']
			);
			$asset = new Chat;
			$asset->content = $attributes['content'];
			$asset->author_id = $attributes['author_id'];
			$asset->author_type = $attributes['author_type'];
			$asset->company_root_id = $company_root_id;
			$asset->active = (isset($attributes['active'])) ? 1 : 0;
			if (isset($attributes['parent_id'])) {
				$asset->parent_id = $attributes['parent_id'];
			}
			$asset->save();
		} else if ($type == 'seo') {
			$company_root_id = self::getCompanyRootIdbyCompanyId(
				$attributes['company_id']
			);
			$asset = new Seo;
			$asset->page_title = $attributes['page_title'];
			$asset->meta_title = $attributes['meta_title'];
			$asset->meta_description = $attributes['meta_description'];
			$asset->canonical_url = $attributes['canonical_url'];
			$asset->company_root_id = $company_root_id;
			$asset->save();
		} else if ($type == 'recruit') {
			$company_root_id = self::getCompanyRootIdbyCompanyId(
				$attributes['company_id']
			);
			$asset = new CloudLogin();
			$asset->xcode = $attributes['xcode'];
			$asset->cloud_user_name = $attributes['cloud_user_name'];
			$asset->cloud_password = Encrypt::encryptString($attributes['cloud_password']);
			$asset->user_name = $attributes['user_name'];
			$asset->password = Hash::make($attributes['password']);
			$asset->cat = $attributes['cat'];
			$asset->board_id = $attributes['board_id'];
			$asset->channel_id = $attributes['channel_id'];
			$asset->upload_frequency = $attributes['upload_frequency'];
			$asset->status = $attributes['status'];
			$asset->notes = $attributes['notes'];
			$asset->ftp_location = self::createFTPPath($attributes['xcode']);
			$asset->company_root_id = $company_root_id;
			$asset->created_by = $attributes['user_id'];
			$asset->save();
		} else if ($type == 'map') {
			if (empty($attributes['markers'])) {
				return false;
			}
			$company_root_id = self::getCompanyRootIdbyCompanyId(
				$attributes['company_id']
			);
			$asset = new Map;
			$asset->lat = $attributes['company_lat'];
			$asset->lng = $attributes['company_lng'];
			$asset->zoom = 17;
			$asset->embed_html = $attributes['companyEmbedURL'];
			$asset->company_root_id = $company_root_id;
			$asset->save();
            foreach ($attributes['markers'] as $marker) {
                $marker = new MapMarker($marker);
                $asset->markers()->save($marker);
            }
			$marker->save();
		}
		return $asset;
	}
	
	private static function createFTPPath($xcode) {
		$upload_config = Config::get('careerone.lux_recruit.dropzone_ftp');
		$ftp_server = $upload_config["server"];
		$ftp_conn = ftp_connect($ftp_server) or die("Could not connect to $ftp_server");
		$login = ftp_login($ftp_conn, $upload_config["username"], $upload_config["password"]);
		$dir = $upload_config["path"].$xcode;
		if (!self::ftp_is_dir($ftp_conn, $dir)) {
			ftp_mkdir($ftp_conn, $dir);
			ftp_mkdir($ftp_conn, $dir.'/error');
			ftp_mkdir($ftp_conn, $dir.'/archived');	
		}
		ftp_close($ftp_conn);
		return $dir;
	}
	
	private static function ftp_is_dir($ftp, $dir) {
		$pushd = ftp_pwd($ftp);
		if ($pushd !== false && @ftp_chdir($ftp, $dir)) {
			ftp_chdir($ftp, $pushd);
			return true;
		}
		return false;
	}
	
	/**
	 * Function exposed for making object
	 * @param string $asset_type
	 * @param array $attributes
	 * return Object
	 */
	public static function makeReply($attributes) {
		$asset = new Chat;
		$asset->content = $attributes['content'];
		$asset->author_id = $attributes['author_id'];
		$asset->author_type = $attributes['author_type'];
		$asset->company_root_id = $attributes['company_root_id'];
		$asset->active = (isset($attributes['active'])) ? 1 : 0;
		$asset->parent_id = $attributes['parent_id'];
		$asset->save();
		return $asset;
	}

	private static function getCompanyRootIdbyCompanyId($company_id) {
		return Company::where('id', '=', $company_id)->pluck('company_root_id');
	}

	/**
	 * Function exposed for destroying object
	 * @param string $asset_type
	 * @param array $attributes
	 * return Object/Null
	 */
	public static function destroy($attributes) {
		$id = (int)$attributes['id'];
		$type = $attributes['type'];
		if ($type == 'media' || $type == 'document' || $type == 'image' || $type == 'video') {
			$asset = Media::find($id);
		} else if ($type == 'news') {
			$asset = News::find($id);
		} else if ($type == 'people') {
			$asset = Person::find($id);
		} else if ($type == 'course') {
			$asset = Course::find($id);
		} else if ($type == 'topic') {
			$asset = Chat::find($id);
		}
		if (isset($asset)) {
			if ($asset->delete()) {
				return true;
			}
		}
		return false;
	}

	public static function saveOrder($sort, $type, $index_page, $per_page) {
		if ($type == 'media') {
			$asset = new Media;
		} else if ($type == 'news') {
			$asset = new News;
		} else if ($type == 'people') {
			$asset = new Person;
		}
		foreach ($sort as $position => $item) {
			$item = str_replace('sort_', '', $item);
			if ($index_page > 0) {
				$position = $index_page * $per_page + $position;
			}
            $asset->where('id', '=', $item)->update(array('order' => $position + 1));
        }
	}

	/**
	 * Private function handles load based on parameters
	 */
	private static function loadByType($attributes) {
		$type = $attributes['type'];
		$company_id = $attributes['company_id'];
		if ($type == 'media') {
			$assets = Media::where('company_id', '=', $company_id);
			$assets->select('id', 'order','title', 'type','featured','active');
		} else if ($type == 'document' || $type == 'image' || $type == 'video') {
			$assets = Media::where('type', '=', $type)->where('company_id', '=', $company_id);
			$assets->select('id', 'order', 'title', 'type','featured','active');
		} else if ($type == 'news') {
			$assets = News::where('company_id', '=', $company_id);
			$assets->select('id', 'order', 'title', 'featured', 'active');
		} else if ($type == 'people') {
			$assets = Person::where('company_id', '=', $company_id);
			$assets->select('id', 'order', 'name as title', 'short_description', 'position', 'active');
		} else if ($type == 'course') {
			$assets = Course::join('company', 'company_root_course.company_root_id', '=', 'company.company_root_id')->where('company.id', '=', $company_id)->whereNull('company.deleted_at');
			$assets->select('company_root_course.id','company_root_course.title', 'company_root_course.summary', 'company_root_course.active');
		} else if ($type == 'topic') {
			$assets = Chat::join('company', 'company_chat.company_root_id', '=', 'company.company_root_id')->where('company.id', '=', $company_id)->whereNull('company.deleted_at');
			$assets->leftjoin('company_chat as company_comment', 'company_chat.parent_id', '=', 'company_comment.id');
			$assets->select('company_chat.id','company_chat.content as title', 'company_chat.active', 'company_chat.parent_id', 'company_comment.content as reply_comment');
			$assets->orderBy('company_chat.company_root_id', 'asc');
			$assets->orderByRaw('CASE WHEN company_chat.parent_id = 0 THEN company_chat.id ELSE concat(company_chat.parent_id, \'-\', company_chat.id) END asc');
		} else if ($type == 'map') {
			$assets = Map::join('company', 'company_root_map.company_root_id', '=', 'company.company_root_id')->where('company.id', '=', $company_id)->whereNull('company.deleted_at');
			$assets->select('company_root_map.id', 'company_root_map.lat', 'company_root_map.lng', 'company_root_map.zoom', 'company_root_map.embed_html', 'company_root_map.map_url');
		}
		return $assets;
	}

	/**
	 * public function handles load based on parameters
	 */
	public static function loadTopics($attributes) {
		$type = $attributes['type'];
		$assets = Chat::leftjoin('company_chat as company_comment', 'company_chat.parent_id', '=', 'company_comment.id');
		$assets->select('company_chat.id','company_chat.content as title', 'company_chat.active', 'company_chat.parent_id', 'company_comment.content as reply_comment');
		$assets->orderBy('company_chat.company_root_id', 'asc');
		$assets->orderByRaw('CASE WHEN company_chat.parent_id = 0 THEN company_chat.id ELSE concat(company_chat.parent_id, \'-\', company_chat.id) END asc');
		return $assets;
	}

	/**
	 * Private function handles load based on parameters
	 */
	private static function loadByID($type, $id) {
		if (
			$type == 'media' || 
			$type == 'document' || 
			$type == 'image' || 
			$type == 'video'
		) {
			$asset = Media::find($id);
		} else if ($type == 'news') {
			$asset = News::find($id);
		} else if ($type == 'people') {
			$asset = Person::find($id);
		} else if ($type == 'course') {
			$asset = Course::find($id);
		} else if ($type == 'topic') {
			$asset = Chat::find($id);
		} else if ($type == 'seo') {
			$asset = Seo::find($id);
		} else
			$asset = Map::find($id);
		}
		return $asset;
	}
}
