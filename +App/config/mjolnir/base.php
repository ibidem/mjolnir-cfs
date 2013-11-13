<?php return array
	(
		'development' => false,

		'domain' => null,
		'path' => '/',

		'protocol' => 'http://', # eg. //, http://, https://
		'timezone' => 'Europe/London',
		'charset' => 'UTF8',
		'lang' => 'en-US',

		// Read & Write for owner, Read for everybody else
		'default.file.permissions' => 0660,

		// Execution access is required to go into the directory
		'default.dir.permissions' => 0770,

		'system' => array
			(
				'title' => 'Untitled',
				'quote' => null,
				'email' => null,
			),

		'logging' => array
			(
				// turning duplication will cause the logging system to relog
				// re-occuring errors based on their main exception message hash
				// with the option off only the first occurance will be recorded
				'duplication' => false,

				// the logging system will replicate all errors based on their
				// level key. So "Notice" errors will get replicated into Notice
				// Hacking will get replicated into Hacking. This can be very
				// efficient way of managing your log. For integrity reasons the
				// master log will still hold the errors regardless
				'replication' => false,

				// the devlogs store development friendly messages and are used
				// during development when the majority of errors are easily
				// identified or for outputing debug information
				'devlogs' => true,

				// you may ignore certain types of log errors if you already
				// an alternative system in place that catches them and reports
				// them to you; one such case are 404 errors
				'exclude' => array
					(
						// empty
					),

				// sometimes you may be recieving errors from underlying or
				// proxy systems outside your control. Often these are caused
				// by broken client side javascript, that makes its way to the
				// the server, insert any regular expression bellow if the main
				// message matches the pattern it will be ignored
				'filter' => array
					(
						// no regex patterns
					),
			),

		// development switches
		'dev:conf' => array
			(
				'raw:js' => false,
			),

		// honeypot settings
		'honeypot' => array
			(
				// if set to false, universal hints via @method calls will be
				// purged; this may help with systems where @return static is
				// natively supported and @method calls are interpreted as
				// "declarations" (at the time of this writing an example of
				// such an editor is phpstorm); ideally place your preference
				// in your private files
				'fluency' => false,
			),

	);
