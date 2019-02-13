Feature: Programmer
  In order to battle projects
  As an API client
  I need to be able to create programmers and power them up

  Background:
     Given the user "weaverryan" exists

  Scenario: Create a programmer
    Given I have the payload:
    """
    {
      "nickname": "ObjectOrienter",
      "avatarNumber" : "2",
      "tagLine": "I'm from a test!"
    }
    """
    When I request "POST /api/programmers"
    Then the response status code should be 201
    And the "Location" header should be "/api/programmers/ObjectOrienter"
    And the "nickname" property should equal "ObjectOrienter"


  Scenario: GET one programmer
    Given the following programmers exist:
      | nickname   | avatarNumber |
      | UnitTester | 3            |
    When I request "GET /api/programmers/UnitTester"
    Then the response status code should be 200
    And the following properties should exist:
    """
    nickname
    avatarNumber
    powerLevel
    tagLine
    """
    And the "nickname" property should equal "UnitTester"

#  Scenario: GET a collection of programmers
#    Given the following programmers exist:
#      | nickname    | avatarNumber |
#      | UnitTester  | 3            |
#      | CowboyCoder | 5            |
#      | scoobydoo   | 2            |
#    When I request "GET /api/programmers"
#    Then the response status code should be 200
#    And the "programmers" property should be an array
#    And the "programmers" property should contain 3 items

  Scenario: PUT to update a programmer
    Given the following programmers exist:
      | nickname    | avatarNumber | tagLine |
      | CowboyCoder | 5            | foo     |
    And I have the payload:
    """
    {
      "nickname": "CowgirlCoder",
      "avatarNumber" : 2,
      "tagLine": "foo"
    }
    """
    When I request "PUT /api/programmers/CowboyCoder"
#    And print last response #uncomment to debug in case of errors
    Then the response status code should be 200
    And the "avatarNumber" property should equal "2"
#    But the "nickname" property should equal "CowgirlCoder"


  Scenario: DELETE a programmer
    Given the following programmers exist:
      | nickname   | avatarNumber |
      | UnitTester | 3            |
    When I request "DELETE /api/programmers/UnitTester"
    Then the response status code should be 204


  Scenario: PATCH to update a programmer
    Given the following programmers exist:
      | nickname    | avatarNumber | tagLine |
      | CowboyCoder | 5            | foo     |
    And I have the payload:
    """
    {
      "tagLine": "giddyup"
    }
    """
    When I request "PATCH /api/programmers/CowboyCoder"
#    And print last response #uncomment to debug in case of errors
    Then the response status code should be 200
    And the "tagLine" property should equal "giddyup"
    And the "avatarNumber" property should equal "5"


  Scenario: Validation errors
    Given I have the payload:
    """
    {
      "tagLine": "I'm from a test!"
    }
    """
    When I request "POST /api/programmers"
    Then the response status code should be 400
    And the following properties should exist:
    """
    type
    title
    errors
    """
    And the "errors.nickname" property should exist
    And the "errors.nickname" property should equal "Please enter a nickname"
    But the "errors.avatarNumber" property should not exist

  Scenario: PATCH to update a programmer
    Given the following programmers exist:
      | nickname    | avatarNumber | tagLine |
      | CowboyCoder | 5            | foo     |
    And I have the payload:
    """
    {
      "tagLine": ""
    }
    """
    When I request "PATCH /api/programmers/CowboyCoder"
#    And print last response #uncomment to debug in case of errors
    Then the response status code should be 400
    And the following properties should exist:
    """
    type
    title
    errors
    """
    And the "Content-Type" header should be "application/problem+json"
    And the "errors.tagLine" property should exist
    And the "errors.tagLine" property should equal "Don't leave tagLine empty"
    But the "errors.avatarNumber" property should not exist
    But the "errors.nickname" property should not exist


  Scenario: Error response on invalid JSON
    Given I have the payload:
    """
    {
      "avatarNumber" : "2
      "tagLine": "I'm from a test!"
    }
    """
    When I request "POST /api/programmers"
    Then the response status code should be 400
    And the "Content-Type" header should be "application/problem+json"
    And the "type" property should equal "invalid_body_format"
