<?php namespace ZeroX\Encoding\Multipart;
use \ZeroX\Encoding\Multipart;
use \ZeroX\Encoding\Multipart\Segment;
use \ZeroX\Mime;

class FormData {
	public static function decode(string $raw, string $boundary = ''): array {
		$form_data = ['files' => [], 'fields' => []];

		// parse multipart data
		$segments = Multipart::decode($raw, $boundary);
		for ($i = count($segments) - 1; $i >= 0; $i--) {
			// parse content-disposition header
			$disp = array_merge_recursive(...array_map(function($v) {
				$kv = explode('=', trim($v), 2);
				return [$kv[0] => count($kv) > 1 ? trim($kv[1], "\"'") : ''];
			}, explode(';', $segments[$i]->getHeader('content-disposition'))));

			// get filename
			$filename = array_key_exists('filename', $disp) ? $disp['filename'] : null;

			// get file mime type
			$filetype = $segments[$i]->getHeader('content-type');
			if ($filetype !== null) {
				$filetype = trim(explode(';', $filetype, 2)[0]);
			}
			if (empty($filetype) && !empty($filename)) {
				$filetype = Mime::getTypeFromFilename($filename);
			}

			// store data
			if (array_key_exists('name', $disp)) {
				if ($filename !== null) {
					$form_data['files'][$disp['name']] = [
						'name' => $filename,
						'type' => $filetype,
						'size' => $segments[$i]->getBodySize(),
						'body' => $segments[$i]->getBody()
					];
				} else {
					$form_data['fields'][$disp['name']] = $segments[$i]->getBody();
				}
			}

			// free segment
			unset($segments[$i]);
		}

		return $form_data;
	}

	public static function encode(array $fields = [], array $files = [], string $boundary): string {
		$segments = [];

		foreach ($fields as $k => $v) {
			$segments[] = static::fieldToSegment($k, $v);
		}

		foreach ($files as $k => $v) {
			if (
				!is_array($v) ||
				!array_key_exists('name', $v) ||
				!array_key_exists('body', $v)
			) continue;

			$type = null;
			if (array_key_exists('type', $v)) {
				$type = $v['type'];
			}
			$segments[] = static::fileToSegment($k, $v['name'], $type, $v['body']);
		}

		return Multipart::encode($segments, $boundary);
	}

	public static function fieldToSegment(string $name, string $value): Segment {
		return new Segment($value, [
			'content-disposition' => sprintf(
				'form-data; name="%s"',
				str_replace(["\\", "\""], ["\\\\", "\\\""], $name)
			)
		]);
	}

	public static function fileToSegment(string $field_name, string $file_name, ?string $type = null, string $value): Segment {
		$headers = [
			'content-disposition' => sprintf(
				'form-data; name="%s"; filename="%s"',
				str_replace(["\\", "\""], ["\\\\", "\\\""], $field_name),
				str_replace(["\\", "\""], ["\\\\", "\\\""], $file_name)
			)
		];

		// add content-type header
		if (empty($type)) {
			$type = Mime::getTypeFromFilename($file_name);
		}
		if (!empty($type)) {
			$headers['content-type'] = $type . '; charset=utf-8';
		}

		return new Segment($value, $headers);
	}
}