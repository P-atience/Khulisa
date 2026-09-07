KHULISA — SME Funding Readiness Platform
Project Overview
KHULISA is a web-based SME Funding Readiness Platform designed to assist small and medium-sized business owners in understanding and improving their readiness for business funding.
The platform evaluates an SME's current business and financial position, identifies areas requiring improvement, and provides tools that help business owners prepare for funding opportunities.
KHULISA provides a central platform where SME owners can complete a funding-readiness assessment, monitor their progress, develop a business plan, prepare cash-flow forecasts, manage supporting documents and view potential funding opportunities.
The system also provides Mentor and Administrator functionality for supporting and managing SMEs on the platform.
 Project Objectives
The main objectives of KHULISA are to:
Assess the funding readiness of SMEs.
Calculate an overall funding-readiness score.
Identify gaps that may prevent an SME from being funding-ready.
Provide recommendations for addressing identified gaps.
Assist SMEs in developing a business plan.
Assist SMEs in preparing cash-flow forecasts.
Allow users to upload and manage supporting documents.
Match SMEs with potentially suitable funding opportunities.
Allow SMEs to track their progress.
Generate funding-readiness reports.
Allow mentors to monitor and support SMEs.
Allow administrators to manage users and funders.
 User Roles
KHULISA supports different types of users.
SME Owner
SME owners can:
Register an account.
Log in securely.
Complete the funding-readiness assessment.
View their readiness score.
View identified gaps.
Create a business plan.
Create a cash-flow forecast.
Upload business documents.
View potential funder matches.
Track their progress.
Generate a funding-readiness report.
Mentor
Mentors can:
Log in to the mentor portal.
View assigned SMEs.
Monitor SME readiness scores.
Review SME progress.
View assessment information.
Identify areas requiring improvement.
Provide guidance and notes.
Administrator
Administrators can:
Access the administration dashboard.
Manage users.
Manage funders.
Monitor platform information.
View SME-related information.
Maintain funding opportunity information.
🛠️ Technologies Used
Frontend
HTML5
CSS3
JavaScript
Backend
PHP
Database
MySQL
Development Environment
XAMPP
Apache
MySQL
phpMyAdmin
Version Control
Git
GitHub
📂 Project Structure
The project follows a structured web-development folder and file convention.
khulisa/
│
├── admin/
│   ├── dashboard.php
│   ├── users.php
│   └── funders.php
│
├── mentor/
│   ├── dashboard.php
│   └── ...
│
├── includes/
│   ├── auth.php
│   ├── functions.php
│   └── db.php
│
├── uploads/
│   └── ...
│
├── css/
│   └── style.css
│
├── index.php
├── login.php
├── register.php
├── dashboard.php
├── assessment.php
├── business-plan.php
├── cashflow.php
├── matches.php
├── documents.php
├── progress.php
├── report.php
├── logout.php
└── README.md
The exact files may change as the system is developed and additional functionality is implemented.
⚙️ Main System Features
1. User Registration and Authentication
Users can register for a KHULISA account and log in using their registered credentials.
Authentication controls access to user-specific functionality and prevents unauthorised users from accessing protected pages.
2. Funding Readiness Assessment
The assessment evaluates several areas of an SME's readiness for funding, including:
Monthly revenue
Separate business bank account
Record keeping
Years operating
Business plan
Cash-flow forecast
Financial statements
Credit score
Tax compliance
Profitability
The information provided by the user is processed by the system to calculate a funding-readiness score.
3. Readiness Scoring
After completing the assessment, KHULISA calculates an overall readiness score.
The assessment results are divided into categories including:
Financial
Documentation
Credit
Governance
Viability
The score provides the SME owner with an indication of their current funding-readiness position.
4. Gap Analysis
KHULISA identifies areas where the SME may require improvement.
Gaps are categorised according to priority, helping users understand which areas should be addressed first.
The system provides recommendations to assist SMEs in improving their readiness.
5. Business Plan Builder
The Business Plan Builder provides structured sections for developing a business plan.
These sections include areas such as:
Business information
Executive summary
Business description
Products and services
Target market
Marketing strategy
Operations
Management
Financial information
6. Cash Flow Builder
The Cash Flow Builder assists SMEs in recording expected income and expenses.
Users can use the tool to understand their projected financial position and improve their financial planning.
7. Document Management
SME owners can upload and manage supporting documentation.
Examples include:
Business registration documents
Financial statements
Tax documents
Business plans
Bank statements
Other supporting documents
Uploaded documents are associated with the relevant user account.
8. Funder Matching
The Funder Matching feature presents funding opportunities that may be relevant to an SME.
Funding opportunities can contain information such as:
Funder name
Funding type
Funding amount
Eligibility requirements
Funding requirements
Suitability/status
9. Progress Tracking
The Progress feature allows SME owners to monitor their journey towards becoming funding-ready.
Users can monitor completed and outstanding activities and identify areas that still require attention.
10. Funding-Readiness Report
KHULISA provides a report containing information about the SME's funding-readiness position.
The report can include:
SME information
Assessment date
Overall readiness score
Category scores
Identified gaps
Recommendations
Funding-readiness status
🗄️ Database
KHULISA uses a MySQL relational database to store and manage application data.
The database supports information relating to:
Users
Assessments
Scores
Gaps
Business plans
Cash-flow information
Documents
Funders
Mentor information
Progress
Foreign-key relationships are used where appropriate to maintain relationships between records.
 Installation and Setup
Prerequisites
Before running KHULISA, install:
XAMPP
PHP
MySQL
A web browser
