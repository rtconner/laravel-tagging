<?php namespace Rtconner\Tagging;

class Tagged extends \Eloquent {

	protected $table = 'tagging_tagged';
	public $timestamps = false;
	protected $softDelete = false;
	protected $fillable = ['tag_name', 'tag_slug'];

	public function taggable() {
		return $this->morphTo();
	}

}