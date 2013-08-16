@mjolnir @cfs
Feature: Cascading File System
  In order for classes to work.
  As a developer
  I need to be able to autoload them dynamically.

  Scenario: Loading a class.
	Given a class file "SimpleExampleClass" with a method hello_world.
	When I call "\app\SimpleExampleClass" and invoke "hello_world" I should get "hello, world".

  Scenario: Loading a class by namespace.
	Given a class file "AnotherExampleClass" with a method hello_world.
	When I call "\mjolnir\testing\AnotherExampleClass" and invoke "hello_world" I should get "hello, world".

  Scenario: Loading a class by group.
	Given a class file "HiddenExampleClass" with a method hello_world.
	When I call "\mjolnir\HiddenExampleClass" and invoke "hello_world" I should get "hello, world".