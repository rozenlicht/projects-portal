# TODO

This document tracks functional and UX improvements for the CEM Project Portal.  
Completed items are marked accordingly.

---

## ✔️ Completed

- [x] Initial prototype implemented using boilerplate.
- [x] Tag support integrated.
- [x] Responsive design confirmed.
- [x] Hyperlinks supported in rich-text fields.
- [x] Query-parameter filtering added.
- [x] Supervisor filtering implemented.
- [x] Support added for external/hybrid projects with industry.
- [x] Test hosting deployed and credentials shared.
- [x] Initial testing confirmed by stakeholders.
- [X] Allow projects to belong to multiple categories (BEP / Internship / Master) via multi-select checkboxes.
- [X] Define expected use (TU/e, companies, institutes).  

---

## 📌 Pending Tasks

### 1. Categorization & Taxonomy
- [ ] Split tags into structured categories:  
  - [ ] **Nature** (mandatory)  
  - [ ] **Focus** (optional)
- [ ] Evaluate whether additional tag categories are needed.

### 2. Project Metadata Enhancements
- [ ] Clarify and redesign the **organization** field:  
  - [ ] Allow multiple organizations per project.  
  - [ ] Possibly provide a preloaded list.
- [ ] Expand supervisor handling:  
  - [ ] Allow adding non-user external supervisors.  
  - [ ] Support hybrid internal + external combinations.  
  - [ ] Consider a dedicated external-supervisor model or free-text entry.

### 3. UI / UX Improvements
- [ ] Add clearer descriptions to field labels.
- [ ] Add example text or placeholders inside form fields.
- [ ] Improve consistency and structure to ensure uniform project entries.
- [ ] Aim for a more guided, “idiot-proof” input process for students.

### 4. Branding & Media
- [ ] Add support for uploading or selecting company/partner logos for collaborative projects.
- [ ] Create a small logo library within the admin.

### 5. Linking & Integration
- [ ] Confirm that filtered listing links work as intended, e.g.:  
  `cem.me.tue.nl/projects?supervisor=joris_remmers`
- [ ] Explore generating filter links automatically inside the admin.

### 6. Collaboration & Review
- [ ] Schedule a review session with Joris and Andreas before the holiday to assess test results and align next steps.

---

## Notes
This TODO list will evolve as more feedback is collected.  
Feel free to reorganize items into GitHub Issues if preferred.
