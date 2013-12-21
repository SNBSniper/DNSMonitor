<?php

class Url extends Eloquent{

	public function client()
	{
		return $this->BelongsTo('Client');
	}
}