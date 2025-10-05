# AGENTS.md

## 🎯 Purpose
This file describes the **agents, tools, and automation rules** used in the EcoPrima repository.  
It helps **Joules** and collaborators understand how to interact with the automated assistants, what they manage, and how they should behave during analysis, refactoring, and deployment cycles.

---

## 🤖 Agent: Joules

**Role:**  
AI assistant responsible for *code analysis, optimization, and refactoring* of the EcoPrima PHP application.

**Scope:**  
- Analyzes backend and frontend code for security, performance, and maintainability.  
- Suggests improvements and generates refactor branches (e.g., `ver1.1`, `ver1.2`, etc.).  
- Follows the project’s versioning pattern (increments subversion on each commit).  

**Rules of Engagement:**  
1. **Environment & Dependencies**  
   - Do *not* modify the hosting environment.  
   - Do *not* add or depend on Composer, `vendor/`, or autoloaders unless explicitly authorized.  
   - Keep database credentials and `config/db.php` unchanged unless user instructs otherwise.  

2. **Tasks Joules Can Perform**  
   - Refactor PHP code for readability and security (SQL injection, XSS, CSRF).  
   - Optimize SQL queries and application flows.  
   - Modernize frontend layouts (HTML5 + CSS3).  
   - Improve UX and visual design consistency.  
   - Propose structured commits and pull requests.

3. **Tasks Joules Must Avoid**  
   - Do not delete or move existing credentials.  
   - Do not modify `.htaccess`, hosting configs, or file permissions.  
   - Do not execute Git or shell commands unless explicitly approved.  

4. **Input / Output Conventions**  
   - **Input:** descriptive prompts from the maintainer specifying goals or branch names.  
   - **Output:**  
     - Human-readable summaries of changes.  
     - Unified diffs or commit-ready patches.  
     - New branch identifiers (`verX.Y`) for each development stage.

5. **Branching & Versioning Policy**  
   - Joules works in isolated branches (e.g., `ver1.1`, `ver1.2`, …).  
   - Each commit increases the minor version sequentially.  
   - Commit messages follow:  
     ```
     feat(ver1.x): <short description>
     ```

---

## 🧰 Other Tools

| Tool | Description | Interaction |
|------|--------------|--------------|
| `EcoPrima WebApp` | PHP-based B2B marketplace for industrial by-products. | Main codebase under active refactoring. |
| `InfinityFree Hosting` | Production environment. | Manual deployment, no Composer. |
| `GitHub` | Repository management and CI/CD placeholder. | Branch and PR operations handled manually by maintainer. |

---

**Tip:**  
Keep this file updated as new automation or agents are introduced.  
It allows Joules and other collaborators to work coherently with your workflow.  
