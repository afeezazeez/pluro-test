
# Accessibility/Compliance Checker API
> An API for checking website accessibility compliance with WCAG (Web Content Accessibility Guidelines)

## Description
This project is a full-stack application built using Laravel for the backend and Vue.js for the frontend. It checks the accessibility of uploaded HTML files against WCAG guidelines and calculates a compliance score.

## Running the App
To run the App, you must have:
- **PHP** (https://www.php.net/downloads)
- **MySQL** (https://www.mysql.com/downloads/)
- **Docker** (https://www.docker.com/products/docker-desktop/)
- **Phpunit**

Clone the repository to your local machine using the command
```console
$ git clone *remote repository url*
```

### Environment
`.env` is generated automatically

`VITE_API_URL=http://localhost:8000/api` is set by default. Please cross-check to ensure it's set.
This is very important to be able to successfully call the backend api

### Booting app
Run the below command.
```
./setup.sh start
```
If you run into permissions error, try the below command, then run actual command again(same goes for other commands to be ran)
```
chmod +x ./setup.sh
```

### Stopping  app

```
./setup.sh stop
```

### Testing
Unit and feature tests were written to ensure the accuracy of the application and the correctness of its core functionality. These tests help verify that the endpoint, data processing logic, and accessibility checks are working as expected. To run the tests, use the following command:
```
$ ./tests.sh
```

### Swagger API Documentation
The documentation for the API can be found- http://localhost:8000/api/documentation



You should be able to visit your app at  http://localhost:8000





### Programming Decisions

I have grouped the accessibility errors into four categories based on the WCAG principles. These categories help organize the issues in a way that aligns with the main accessibility criteria while keeping them focused on common issues found in web content.

1. **Text Alternatives** (Falls under **Perceivable**):  
   This category includes issues related to missing text alternatives for non-text content. In this case, the errors occur when `img` elements are missing the `alt` attribute, which is essential for screen readers to convey the image content to visually impaired users.

2. **Adaptable** (Falls under **Perceivable**):  
   The adaptable category focuses on issues where the web content does not follow a proper structure that allows it to be adapted to different devices or assistive technologies. For example, incorrect header nesting, where a `<h3>` follows an `<h1>`, is considered a violation.

3. **Navigable** (Falls under **Operable**):  
   This category addresses issues that affect the user's ability to navigate through the website using various input methods like a keyboard or screen reader. An example is anchor tags that contain no text (`<a href="https://goal.com"></a>`), which are not meaningful to screen readers or users navigating by keyboard.

4. **Distinguishable** (Falls under **Perceivable**):  
   This category focuses on ensuring that text is distinguishable from the background to meet contrast standards. Issues in this group involve checking the contrast ratio between text and its background to ensure it is readable by users with visual impairments. For instance, low contrast between text and background color violates the contrast requirements.

### Design Document
The design document can be found at https://docs.google.com/document/d/1Vdcz0F8g8Qs2wiPxeBdPQ9DE1JwsQ_iRz_CVaNJwi2w/edit?tab=t.0 This document explains the architectural decision and approach and also the scoring logic implemented.
