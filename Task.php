<?php namespace mjolnir\cfs;

/**
 * @package    mjolnir
 * @category   Cascading File System
 * @author     Ibidem Team
 * @copyright  (c) 2012 Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Task
{
	/**
	 * @return static
	 */
	static function invoke($encoded_task)
	{
		$class_name = '\app\Task';
		$class_segments = \explode(':', $encoded_task);

		foreach ($class_segments as $segment)
		{
			$class_name .= '_'.\ucfirst($segment);
		}

		return $class_name::instance()
			->writer_is(\app\SilentWriter::instance());
	}

	/**
	 * Adds common console formatters to provided writer.
	 */
	static function consolewriter(\mjolnir\types\Writer $writer)
	{
		$writer->addformat
			(
				'status',
				function ($writer, $status, $message)
				{
					$writer->writef('%12s %s', '['.$status.']', $message);
				}
			);

		$writer->addformat
			(
				'title',
				function ($writer, $title)
				{
					$writer
						->writef(' %s', $title)->eol()
						->writef(' %s ', \str_repeat('-', $writer->get('width', 80) - 2))->eol()
						->eol();
				}
			);

		$writer->addformat
			(
				'error',
				function ($writer, $text)
				{
					$writer->stderr_writef(' %10s %s', '[Error]', $text);
				}
			);

		$writer->addformat
			(
				'subtitle',
				function ($writer, $title)
				{
					$writer->writef
						(
							' ===[ '.$title.' ]'.\str_repeat('=', $writer->get('width', 80) - 9 - \strlen($title))
						)
						->eol()
						->eol();
				}
			);

		$writer->addformat
			(
				'wrap',
				function ($writer, $text, $indent = 0, $nowrap_hint = null)
				{
					if ($indent !== 0)
					{
						$width = $writer->get('width', 80);

						$indent_text = \str_repeat(' ', $indent);
						$text = \wordwrap
							(
								$text,
								$width - $indent,
								$writer->eolstring().$indent_text
							);

						$text = $indent_text.$text;
					}

					if ($nowrap_hint !== null)
					{
						$text = \str_replace($nowrap_hint, ' ', $text);
					}
					
					$writer->writef('%s', $text);
				}
			);

		$writer->addformat
			(
				'list',
				function ($writer, $term, $definition, $indent_hint = null, $nowrap_hint = null)
				{
					$width = $writer->get('width', 80);

					if ( ! $indent_hint)
					{
						$indent_hint = \strlen($term);
					}

					$text = $term.$definition;
					if (\strlen($text) >= $width)
					{
						$firstline = \substr($text, 0, $width);
						$otherlines = \substr($text, $width);

						if ($otherlines[0] !== ' ')
						{
							// imperfect cut
							$lastspace = \strrpos($firstline, ' ');
							$extra = \substr($firstline, $lastspace);
							$firstline = \substr($firstline, 0, $lastspace);
							$otherlines = $extra.$otherlines;
						}

						$otherlines = \trim($otherlines);

						if ($nowrap_hint)
						{
							$firstline = \str_replace($nowrap_hint, ' ', $firstline);
						}

						$writer->writef($firstline)->eol();

						$indented_text = \wordwrap
							(
								$otherlines,
								$width - $indent_hint,
								$writer->eolstring().\str_repeat(' ', $indent_hint)
							);

						if ($nowrap_hint)
						{
							$indented_text = \str_replace($nowrap_hint, ' ', $indented_text);
						}

						$writer->writef
							(
								\str_repeat(' ', $indent_hint).
								$indented_text
							);
					}
					else # word not longer then width
					{
						if ($nowrap_hint)
						{
							$text = \str_replace($nowrap_hint, ' ', $text);
						}

						$writer->writef($text);
					}
				}
			);

		$writer->addformat
			(
				'reset',
				function ($writer)
				{
					$writer->writef("\r");
					$writer->writef(\str_repeat(' ', 80));
					$writer->writef("\r");
				}
			);
	}

} # class
