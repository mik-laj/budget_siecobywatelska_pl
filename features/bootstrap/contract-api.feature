Feature: Category API
  In order to maintain the contracts shown on the site and allow other systems to integrate with us
  As an API user
  I need to have REST/CRUD API

  Background:
    Given the following categories exist:
      | parent    | title         |
      |           | Wydatki       |
      | Wydatki   | Biuro         |
      | Biuro     | Dlugopisy     |
      | Biuro     | Papier        |
      | Wydatki   | IT            |

    Given the following contracts exist:
      | conclusionAt | supplier | title                | value  | category  |
      | 10-12-2015   | ADG      | Sprzet komputerowy   | 10000  | IT        |
      | 08-10-2014   | LFF      | Niebieskie flamastry | 110    | Dlugopisy |
      | 10-12-2015   | LFF      | Arkusze A4           | 200    | Papier    |
      | 10-10-2015   | ADG      | Serwery              | 10000  | IT        |
      | 10-8-2016    | ABC      | Pracownik            | 10000  | IT        |

  Scenario: Create a contract
    Given I have the payload:
      """
      {
        "contract": {
          "conclusionAt": "10-12-2015",
          "supplier": "123",
          "title": "Umowa niebieska",
          "value": "123",
          "category": {
            "id": 1
          }
        }
      }
      """
    When I request "POST /api/contracts"
    Then the response status code is 201
    And the "Location" header is "http://127.0.0.1:8000/api/contracts/6"
    And I request "GET /api/contracts/6"
    And the response status code is 200
    And the following properties should exist:
      """
      id
      title
      """
    And the "title" property is equal "Serwery"
    And the link "parent" should exist and its value is "http://127.0.0.1:8000/api/contracts/5"

  Scenario: Validation errors
    Given I have the payload:
      """
      {
        "category": {
        }
      }
      """
    When I request "POST /api/contracts"
    Then the response status code is 400
    And the "children.title.errors" property is exist
#    And the "Content-Type" header is "application/problem+json"

  Scenario: Error response on invalid JSON
    Given I have the payload:
      """
      {
        "category": {
          "title: ""
        }
      }
      """
    When I request "POST /api/contracts"
    Then the response status code is 400
#    And the "Content-Type" header is "application/problem+json"
#    And the "type" property is contain "/api/docs/errors#invalid_body_format"

  Scenario: Proper 404 exception on no contracts
    When I request "GET /api/contracts/44999444"
    Then the response status code is 404
#    And the "Content-Type" header is "application/problem+json"
#    And the "type" property is equal "about:blank"
#    And the "title" property is equal "Not Found"
#    And the "detail" property is equal "The programmer fake does not exist!"

  Scenario: GET one contract
    When I request "GET /api/contracts/2"
    Then the response status code is 200
    And the following properties should exist:
      """
      id
      title
      """
    And the "title" property is equal "Biuro"
    And the link "self" should exist and its value is "http://127.0.0.1:8000/api/categories/2"
    And the link "children" should exist and its value is "http://127.0.0.1:8000/api/categories/2/children"
    And the link "parent" should exist and its value is "http://127.0.0.1:8000/api/categories/1"


  Scenario: GET a collection of contracts
    When I request "GET /api/categories.json"
    Then the response status code is 200
    And the embedded list should contain 5 item
    And scope into the first embedded item
    And the following properties should exist:
    """
    id
    title
    _links
    """

  Scenario: PUT to update a category
    And I have the payload:
      """
      {
        "category": {
          "title": "Materiał eksploatacyjny do drukarki",
          "parent": {
            "id" : 5
          }
        }
      }
      """
    When I request "PUT /api/categories/4"
    And the "Location" header is "http://127.0.0.1:8000/api/categories/4"
    Then the response status code is 204
    And I request "GET /api/categories/4"
    And the "title" property is equal "Materiał eksploatacyjny do drukarki"
    And the link "parent" should exist and its value is "http://127.0.0.1:8000/api/categories/5"

  Scenario: PUT to create a category
    And I have the payload:
      """
      {
        "category": {
          "title": "Materiał eksploatacyjny do drukarki",
          "parent": {
            "id" : 5
          }
        }
      }
      """
    When I request "PUT /api/categories/20"
    Then the response status code is 201
    And the "Location" header is "http://127.0.0.1:8000/api/categories/6"
    And I request "GET /api/categories/6"
    And the "title" property is equal "Materiał eksploatacyjny do drukarki"
    And the link "parent" should exist and its value is "http://127.0.0.1:8000/api/categories/5"


  Scenario: PATCH to update a programmer
    And I have the payload:
      """
      {
        "category": {
          "title": "Materiał eksploatacyjny do drukarki",
          "parent": {
            "id" : 5
          }
        }
      }
      """
    When I request "PATCH /api/categories/4"
    Then the response status code is 200
    And the "Location" header is "http://127.0.0.1:8000/api/categories/4"
    And I request "GET /api/categories/4"
    And the "title" property is equal "Materiał eksploatacyjny do drukarki"
    And the link "parent" should exist and its value is "http://127.0.0.1:8000/api/categories/5"

  Scenario: DELETE a category
    When I request "DELETE /api/categories/4"
    Then the response status code is 204
    Then I request "GET /api/categories/4"
    Then the response status code is 404
