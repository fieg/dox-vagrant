Feature: up
  In order to setup a dev env quickly
  As a developer
  I need to be able to start the environment

Scenario: Bring environment up
  Given I have a Doxfile with:
  """
  web1: ~
  """
  And a container named "web1" does not exist
  When I run "up"
#  Then I should see "Bringing environment up..."
#  And I should see "==> web1: Creating docker image..."
#  And I should see "==> web1: Booting container..."
  And an image named "web1:latest" should exist
  And a container named "web1" should be running
  When I GET '/static/index.html'
  Then I should see "Hello static world"
  When I GET '/static/'
  Then I should see "Hello static world"

Scenario: Bring environment up with php
  Given I have a Doxfile with:
  """
  web1: ~
  app1: ~
  """
  And a container named "web1" does not exist
  And a container named "app1" does not exist
  When I run "up"
#  Then I should see "Bringing environment up..."
#  And I should see "==> app1: Creating docker image..."
#  And I should see "==> app1: Booting container..."
#  And I should see "==> web1: Creating docker image..."
#  And I should see "==> web1: Booting container..."
  And a container named "web1" should be running
  And a container named "app1" should be running
  When I GET '/'
  Then I should see "Hello php world"
  When I GET '/something'
  Then I should see "Hello php world"
