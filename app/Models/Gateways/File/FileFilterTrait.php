<?php
namespace Application\Models\Gateways\File;


trait FileFilterTrait
{
	/**
	 * Filters type of the record
	 * @param string $type
	 * @param Array $list
	 * @return Array
	 */
	public function filterType($type, $list)
	{
		$filtered_list = array();
		
		foreach ($list as $key => $file) {
			if ($type == 'EREC') {
				if (is_array($file)) {
					$filtered_list[$key] = $file;
				}
			} else if ($type == 'ALL') {
				$filtered_list[$key] = $file;
			}
		}

		return $filtered_list;
	}

	/**
	 * Filters file type on file gateway
	 * @param string $filetype
	 * @param Array $list
	 * @return Array
	 */
	public function filterFileType($filetype, Array $list)
	{
		$filtered_list = array();
		foreach ($list as $f) {
			if ($filetype == 'txt') {
				if ((pathinfo($f, PATHINFO_EXTENSION) == 'txt') && (!is_dir($f))) {
					$filtered_list[] = $f;
				}
			} else if ($filetype == 'attachments') {
				if ((pathinfo($f, PATHINFO_EXTENSION) != 'txt') && (!is_dir($f))) {
					$filtered_list[] = $f;
				}
			}
		}
		return $filtered_list;
	}

	/**
	 * Filters action on file gateway
	 * @param string $action
	 * @param Array $list
	 * @return Array
	 */
	public function filterAction($action, Array $list)
	{
		$actioned_list = array();

		return $actioned_list;
	}

	

	



}

