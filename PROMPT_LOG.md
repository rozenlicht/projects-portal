## INITIAL PLAN PROMPT

We are going to build something beautiful in this pristine Filament4 scaffold!

We are going to build a small app that helps our department at Eindhoven University of Technology to showcase and manage available projects.

The Projects are always one of the following:
- Internship
- Bachelor Thesis Project
- Master Thesis Project

We can use some enum I think to define this.

We have staff members in Users that can manage the projects. We will make use of a few roles and permissions, using Spatie Permissions. The roles will be:
- Administrator
- Supervisor

The supervisors can manage their own projects (project_owner_id) and the projects they supervise ( M:N with user with an order_rank column). Make sure the filament setup later on supports easy reshuffling of supervisors.

The Project will have the following fields:
- Name
- student_name & student_email (nullables, to register if they are taken or not)
- Featured Image
- short_description
- richtext_content
- Tags

We'll make Tags managed. You can pick one or more tags from these categories:
- Group (.e.g with professor)
- Nature (Experimental/Numerical/..)
- Focus (Metals, Steel, 3D printing, Meta materials, Nature-inspired, Damage models, Simulation development, etc)

We want to be able to manage those tags as Admins.

Then on the front-end side we want a public light-mode-only, Tailwind v4 based app that has:
- A nice welcome 
- Research Projects (drop down: Bachelor Thesis Projects, Internships, Master Thesis Projects)
- Past projects
- Contact

Make a nice 2 x N overview of projects using cards with the featured image. Include the title and the short description.
Bottom line, include the supervisors (round avatar and name).

Clicking the card brings you to the project. Use Spatie Sluggable on the title to make nice URLs. 

In the Project detail page, include the text etc and at the bottom again include the supervisors. Make sure to have a way to contact them by e-mail. (consider we dont want to get spammed so take some security measures here)

Use the MoM Logo and tue_logo (see assets).
Color primary: #7fabc9.

Make a plan and we'll get started.