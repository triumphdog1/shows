<?php

class Gig
{
	private $id;
	private $datetime;
	private $city;
	private $venue;
	private $info;
	private $tickets;
        
	public function __construct($datetime, $city, $venue, $info, $tickets, $id=null)
	{
            $this->datetime($datetime);
            $this->city($city);
            $this->venue($venue);
            $this->info($info);
            $this->tickets($tickets);
            if (isset($id)) $this->id($id);
	}
        
        public function id($a=null) {
            if ($a === null) return $this->id;
            $this->id = $a;
        }
        
        public function datetime($a=null) {
            if ($a === null) return $this->datetime;
            if (!is_numeric($a)) $a = strtotime($a);
            $this->datetime = $a;
        }
        
        public function date($a=null) {
            if ($a === null) return date('n/j/Y', $this->datetime() );
            $a = date('n/j/Y', strtotime($a) );
            $this->datetime( strtotime( $a . " " . $this->time() ) );
        }

        public function time($a=null) {
            if ($a === null) return date('g:i a', $this->datetime() );
            $this->datetime( strtotime( $this->date() . " " . $a) );
        }
        
        public function city($a=null) {
            if ($a === null) return html_entity_decode($this->city);
            $this->city = trim( htmlentities($a, ENT_QUOTES) );
        }
        
        public function venue($a=null) {
            if ($a === null) return html_entity_decode($this->venue);
            $this->venue = trim( htmlentities($a, ENT_QUOTES) );
        }
        
        public function info($a=null) {
            if ($a === null) return html_entity_decode($this->info);
            $this->info = trim( htmlentities($a, ENT_QUOTES) );
        }
        
        public function tickets($a=null) {
            if ($a === null) return html_entity_decode($this->tickets);
            $this->tickets = trim( htmlentities($a, ENT_QUOTES) );
        }

        public function ticketLink() {
            if (substr($this->tickets(), 0 , 7) == 'http://') {
		return "<a href='" . $this->tickets() . "' target='_NEW'><img src='images/tickets_button.gif'></a>";
            }
            return $this->tickets();
        }
        
        public function makeArray() {
            $a = array();
            $a['id'] = $this->id();
            $a['date'] = $this->date();
            $a['time'] = $this->time();
            $a['city'] = $this->city();
            $a['venue'] = $this->venue();
            $a['info'] = $this->info();
            $a['tickets'] = $this->ticketLink();
            return $a;
        }
        
}

?>
