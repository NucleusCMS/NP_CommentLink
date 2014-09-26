<?php
class NP_CommentLink extends NucleusPlugin {

	function getName() {
		return 'CommentLink';
	}


	function getAuthor() {
		return 'pushman'; 
	}

	function getURL() {
		return 'http://blog.heartfield-web.com/download/NP_CommentLink.html';
	}

	function getVersion() {
		return '1.1';
	}

	function getDescription() { 
		return _COMMENTLINK_Description;
	}

	function getEventList() {
		return array('PreComment');
	}

	//put in support for SqlTablePrefix, needed in 2.0
	function supportsFeature($feature) {
		switch($feature) {
			case 'SqlTablePrefix':
				return 1;
			default:
				return 0;
		}
	}

	function init() { 
		// include language file for this plugin
		$language = ereg_replace( '[\\|/]', '', getLanguageName());
		if (file_exists($this->getDirectory().$language.'.php')) {
			include_once($this->getDirectory().$language.'.php');
		} else {
			include_once($this->getDirectory().'english.php');
		}
	}

	function install() {
		$this->createBlogOption('guestMail', _COMMENTLINK_BlogOption, 'yesno', 'yes');
		$this->createMemberOption('linkControl', _COMMENTLINK_MemberOption, 'select', '3', $typeExtras =_COMMENTLINK_MemberOption0 . "|0|" . _COMMENTLINK_MemberOption1 . "|1|" . _COMMENTLINK_MemberOption2 . "|2|" . _COMMENTLINK_MemberOption3 . "|3");
	}

	function event_PreComment(&$data) {
		global $blogid, $member;
		$blogid = intval($blogid);

		if ($this->getBlogOption($blogid, 'guestMail') == 'yes') {
// original code from http://blog.cles.jp/
			if ( ! $member->isLoggedIn() ) {
				if ( stristr($data['comment']['userlinkraw'],'mailto:') !== FALSE ) {
					$data['comment']['userlinkraw'] = '';
				}
			}
		}

		if ($data['comment']['memberid'] != 0) {

			$mname = MEMBER::createFromID($data['comment']['memberid']);

			$data['comment']['user'] = $mname->getRealName();

			$linkControl = $this->getMemberOption($data['comment']['memberid'], 'linkControl');

			switch ($linkControl) {
				case 0:
					$data['comment']['userlinkraw'] = "";
					break;
				case 1:
					$data['comment']['userlinkraw'] = $mname->getURL();
					break;
				case 2:
					$data['comment']['userlinkraw'] = 'mailto:' . $mname->getEmail();
					break;
				case 3:
				default:
					break;
			}

		}

	}

}
?>