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

		'cfs' => array
			(
				// uses interface.namespace.matchers on namespaces, if matched
				// the namespace is aliased to an empty interface; this
				// effectively results in all interfaces being the same
				// namespace and having no methods on them; ie. emulates
				// removal of all interface declarations within the system for
				// namespaces matching rules in interface.namespace.matchers
				'instant.interfaces' => false,

				// matchers for detecting interfaces; if a namespace does not
				// match any of the rules bellow the instant.interface rule
				// will not apply
				'interface.namespace.matchers' => array
					(
						// mjolnir types
						'#mjolnir\\\types#'
					),
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

				// the short.log or devlog stores a 1-line version of the error
				// this is very useful in development where most errors can be
				// easily identified in a few words and don't need to be stored
				'short.log' => true,

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

	);
