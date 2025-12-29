# MÔ HÌNH THỰC THỂ KẾT HỢP (ERD - Entity Relationship Diagram)

## HỆ THỐNG QUẢN LÝ DỰ ÁN TASKFLOW

---

## 1. SƠ ĐỒ ERD TỔNG QUAN

```
┌─────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│                                    TASKFLOW DATABASE - ERD DIAGRAM                                          │
│                                         16 Entities - MySQL 8.0                                             │
└─────────────────────────────────────────────────────────────────────────────────────────────────────────────┘


                                              ┌──────────────────────┐
                                              │      LABELS          │
                                              ├──────────────────────┤
                                              │ PK  id               │
                                              │     name             │
                                              │     color            │
                                              │     description      │
                                              │     created_at       │
                                              └──────────┬───────────┘
                                                         │
                                                         │ 1
                                                         │
                                                         │ N
                                              ┌──────────┴───────────┐
                                              │    TASK_LABELS       │
                                              ├──────────────────────┤
                                              │ PK,FK task_id        │
                                              │ PK,FK label_id       │
                                              │     created_at       │
                                              └──────────┬───────────┘
                                                         │ N
                                                         │
                                                         │ 1
┌──────────────────────┐                      ┌──────────┴───────────┐                      ┌──────────────────────┐
│       USERS          │                      │       TASKS          │                      │   TASK_CHECKLISTS    │
├──────────────────────┤                      ├──────────────────────┤                      ├──────────────────────┤
│ PK  id               │◄─────────┐           │ PK  id               │                      │ PK  id               │
│     email            │          │           │ FK  project_id       │──────────┐           │ FK  task_id          │──┐
│     password_hash    │          │           │     title            │          │           │     title            │  │
│     full_name        │          │           │     description      │          │           │     is_completed     │  │
│     avatar_url       │          │           │     status           │          │           │     position         │  │
│     role             │          │           │     priority         │          │           │     completed_at     │  │
│     department       │          │           │     position         │          │           │ FK  completed_by     │──┼──┐
│     position         │          │           │     start_date       │          │           │     created_at       │  │  │
│     phone            │          │           │     due_date         │          │           └──────────────────────┘  │  │
│     is_active        │          │           │     completed_at     │          │                                     │  │
│     email_verified   │          │           │     estimated_hours  │          │                                     │  │
│     remember_token   │          │           │     actual_hours     │          │                                     │  │
│     last_login_at    │          │           │ FK  created_by       │──────────┼─────────────────────────────────────┘  │
│     created_at       │          │           │     created_at       │          │                                        │
│     updated_at       │          │           │     updated_at       │          │                                        │
└──────────┬───────────┘          │           └──────────┬───────────┘          │                                        │
           │                      │                      │                      │                                        │
           │                      │                      │                      │                                        │
           │ 1                    │                      │ 1                    │                                        │
           │                      │                      │                      │                                        │
           │ N                    │                      │ N                    │                                        │
┌──────────┴───────────┐          │           ┌──────────┴───────────┐          │                                        │
│  PROJECT_MEMBERS     │          │           │   TASK_ASSIGNEES     │          │                                        │
├──────────────────────┤          │           ├──────────────────────┤          │                                        │
│ PK,FK project_id     │──┐       │           │ PK,FK task_id        │          │                                        │
│ PK,FK user_id        │──┼───────┤           │ PK,FK user_id        │──────────┼────────────────────────────────────────┤
│     role             │  │       │           │ FK  assigned_by      │──────────┼────────────────────────────────────────┤
│     joined_at        │  │       │           │     assigned_at      │          │                                        │
└──────────────────────┘  │       │           └──────────────────────┘          │                                        │
                          │       │                                             │                                        │
           ┌──────────────┘       │                                             │                                        │
           │                      │                                             │                                        │
           │ N                    │                                             │                                        │
           │                      │                                             │                                        │
           │ 1                    │                                             │                                        │
┌──────────┴───────────┐          │           ┌──────────────────────┐          │                                        │
│      PROJECTS        │          │           │      DOCUMENTS       │          │                                        │
├──────────────────────┤          │           ├──────────────────────┤          │                                        │
│ PK  id               │◄─────────┼───────────│ FK  project_id       │          │                                        │
│     name             │          │           │ PK  id               │          │                                        │
│     description      │          │           │     name             │          │                                        │
│     color            │          │           │     description      │          │                                        │
│     icon             │          │           │     type             │          │                                        │
│     status           │          │           │     mime_type        │          │                                        │
│     priority         │          │           │     file_size        │          │                                        │
│     progress         │          │           │     file_path        │          │                                        │
│     start_date       │          │           │ FK  parent_id        │◄─────────┼──┐ (Self-referencing)                  │
│     end_date         │          │           │     is_starred       │          │  │                                     │
│     budget           │          │           │     download_count   │          │  │                                     │
│ FK  created_by       │──────────┤           │ FK  uploaded_by      │──────────┼──┼─────────────────────────────────────┤
│     created_at       │          │           │     created_at       │          │  │                                     │
│     updated_at       │          │           │     updated_at       │          │  │                                     │
└──────────┬───────────┘          │           └──────────┬───────────┘          │  │                                     │
           │                      │                      │                      │  │                                     │
           │                      │                      │ 1                    │  │                                     │
           │                      │                      │                      │  │                                     │
           │                      │                      │ N                    │  │                                     │
           │                      │           ┌──────────┴───────────┐          │  │                                     │
           │                      │           │   DOCUMENT_SHARES    │          │  │                                     │
           │                      │           ├──────────────────────┤          │  │                                     │
           │                      │           │ PK,FK document_id    │          │  │                                     │
           │                      │           │ PK,FK user_id        │──────────┼──┼─────────────────────────────────────┤
           │                      │           │     permission       │          │  │                                     │
           │                      │           │     shared_at        │          │  │                                     │
           │                      │           │ FK  shared_by        │──────────┼──┼─────────────────────────────────────┤
           │                      │           └──────────────────────┘          │  │                                     │
           │                      │                                             │  │                                     │
           │                      │                                             │  │                                     │
           │ 1                    │                                             │  │                                     │
           │                      │                                             │  │                                     │
           │ N                    │                                             │  │                                     │
┌──────────┴───────────┐          │           ┌──────────────────────┐          │  │                                     │
│   CALENDAR_EVENTS    │          │           │      COMMENTS        │          │  │                                     │
├──────────────────────┤          │           ├──────────────────────┤          │  │                                     │
│ PK  id               │          │           │ PK  id               │          │  │                                     │
│     title            │          │           │     entity_type      │          │  │                                     │
│     description      │          │           │     entity_id        │          │  │                                     │
│     type             │          │           │     content          │          │  │                                     │
│     color            │          │           │ FK  parent_id        │◄─────────┼──┼──┐ (Self-referencing for replies)   │
│     start_time       │          │           │ FK  created_by       │──────────┼──┼──┼─────────────────────────────────┤
│     end_time         │          │           │     created_at       │          │  │  │                                  │
│     is_all_day       │          │           │     updated_at       │          │  │  │                                  │
│     location         │          │           └──────────────────────┘          │  │  │                                  │
│ FK  project_id       │──────────┤                                             │  │  │                                  │
│ FK  task_id          │──────────┼─────────────────────────────────────────────┘  │  │                                  │
│ FK  created_by       │──────────┤                                                │  │                                  │
│     created_at       │          │                                                │  │                                  │
│     updated_at       │          │                                                │  │                                  │
└──────────┬───────────┘          │                                                │  │                                  │
           │                      │                                                │  │                                  │
           │ 1                    │                                                │  │                                  │
           │                      │                                                │  │                                  │
           │ N                    │                                                │  │                                  │
┌──────────┴───────────┐          │           ┌──────────────────────┐             │  │                                  │
│   EVENT_ATTENDEES    │          │           │    NOTIFICATIONS     │             │  │                                  │
├──────────────────────┤          │           ├──────────────────────┤             │  │                                  │
│ PK,FK event_id       │          │           │ PK  id               │             │  │                                  │
│ PK,FK user_id        │──────────┼───────────│ FK  user_id          │─────────────┼──┼──────────────────────────────────┤
│     status           │          │           │     type             │             │  │                                  │
│     responded_at     │          │           │     title            │             │  │                                  │
└──────────────────────┘          │           │     message          │             │  │                                  │
                                  │           │     data             │             │  │                                  │
                                  │           │     is_read          │             │  │                                  │
                                  │           │     read_at          │             │  │                                  │
                                  │           │     created_at       │             │  │                                  │
                                  │           └──────────────────────┘             │  │                                  │
                                  │                                                │  │                                  │
                                  │           ┌──────────────────────┐             │  │                                  │
                                  │           │    ACTIVITY_LOGS     │             │  │                                  │
                                  │           ├──────────────────────┤             │  │                                  │
                                  │           │ PK  id               │             │  │                                  │
                                  └───────────│ FK  user_id          │─────────────┼──┼──────────────────────────────────┘
                                              │     entity_type      │             │  │
                                              │     entity_id        │             │  │
                                              │     action           │             │  │
                                              │     old_values       │             │  │
                                              │     new_values       │             │  │
                                              │     ip_address       │             │  │
                                              │     user_agent       │             │  │
                                              │     created_at       │             │  │
                                              └──────────────────────┘             │  │
                                                                                   │  │
                                              ┌──────────────────────┐             │  │
                                              │    USER_SETTINGS     │             │  │
                                              ├──────────────────────┤             │  │
                                              │ PK,FK user_id        │─────────────┘  │
                                              │     theme            │                │
                                              │     language         │                │
                                              │     timezone         │                │
                                              │     notification_*   │                │
                                              │     updated_at       │                │
                                              └──────────────────────┘                │
                                                                                      │
                                                                                      │
                                              (Tất cả FK user_id đều tham chiếu ◄─────┘
                                               đến bảng USERS)
```


---

## 2. SƠ ĐỒ ERD CHI TIẾT THEO NHÓM CHỨC NĂNG

### 2.1. Nhóm Quản lý Người dùng (User Management)

```
┌─────────────────────────────────────────────────────────────────────────────────────┐
│                              USER MANAGEMENT MODULE                                  │
└─────────────────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────┐           ┌─────────────────────────────────────┐
│              USERS                  │           │          USER_SETTINGS              │
├─────────────────────────────────────┤           ├─────────────────────────────────────┤
│ «PK» id          : VARCHAR(36)      │           │ «PK,FK» user_id  : VARCHAR(36)      │
│ «UK» email       : VARCHAR(255)     │ 1       1 │ theme            : ENUM             │
│ password_hash    : VARCHAR(255)     │───────────│ language         : VARCHAR(10)      │
│ full_name        : VARCHAR(100)     │           │ timezone         : VARCHAR(50)      │
│ avatar_url       : VARCHAR(500)     │           │ notification_email    : TINYINT     │
│ role             : ENUM             │           │ notification_push     : TINYINT     │
│ department       : VARCHAR(100)     │           │ notification_task_assigned: TINYINT │
│ position         : VARCHAR(100)     │           │ notification_task_due : TINYINT     │
│ phone            : VARCHAR(20)      │           │ notification_comment  : TINYINT     │
│ is_active        : TINYINT(1)       │           │ notification_mention  : TINYINT     │
│ email_verified_at: DATETIME         │           │ updated_at       : DATETIME         │
│ remember_token   : VARCHAR(64)      │           └─────────────────────────────────────┘
│ remember_token_expiry: DATETIME     │
│ last_login_at    : DATETIME         │
│ created_at       : DATETIME         │
│ updated_at       : DATETIME         │
├─────────────────────────────────────┤
│ «INDEX» idx_users_email             │
│ «INDEX» idx_users_role              │
│ «INDEX» idx_users_is_active         │
│ «INDEX» idx_users_department        │
└─────────────────────────────────────┘
         │
         │ 1
         │
         │ N
┌────────┴────────────────────────────┐
│          NOTIFICATIONS              │
├─────────────────────────────────────┤
│ «PK» id          : VARCHAR(36)      │
│ «FK» user_id     : VARCHAR(36)      │
│ type             : VARCHAR(50)      │
│ title            : VARCHAR(255)     │
│ message          : TEXT             │
│ data             : JSON             │
│ is_read          : TINYINT(1)       │
│ read_at          : DATETIME         │
│ created_at       : DATETIME         │
├─────────────────────────────────────┤
│ «INDEX» idx_notif_user              │
│ «INDEX» idx_notif_is_read           │
│ «INDEX» idx_notif_type              │
└─────────────────────────────────────┘


ROLE ENUM Values:
┌─────────────────────────────────────┐
│ • admin    - Quản trị viên          │
│ • manager  - Quản lý                │
│ • member   - Thành viên             │
│ • guest    - Khách                  │
└─────────────────────────────────────┘

THEME ENUM Values:
┌─────────────────────────────────────┐
│ • light    - Giao diện sáng         │
│ • dark     - Giao diện tối          │
│ • system   - Theo hệ thống          │
└─────────────────────────────────────┘
```

### 2.2. Nhóm Quản lý Dự án (Project Management)

```
┌─────────────────────────────────────────────────────────────────────────────────────┐
│                             PROJECT MANAGEMENT MODULE                                │
└─────────────────────────────────────────────────────────────────────────────────────┘

                                    ┌─────────────────────────────────────┐
                                    │              USERS                  │
                                    ├─────────────────────────────────────┤
                                    │ «PK» id          : VARCHAR(36)      │
                                    │ ...                                 │
                                    └──────────────────┬──────────────────┘
                                                       │
                              ┌────────────────────────┼────────────────────────┐
                              │                        │                        │
                              │ 1                      │ 1                      │ 1
                              │                        │                        │
                              │ N                      │ N                      │ N
┌─────────────────────────────┴─────┐    ┌─────────────┴─────────────┐    ┌─────┴─────────────────────────────┐
│          PROJECTS                 │    │     PROJECT_MEMBERS       │    │          DOCUMENTS                │
├───────────────────────────────────┤    ├───────────────────────────┤    ├───────────────────────────────────┤
│ «PK» id          : VARCHAR(36)    │    │ «PK,FK» project_id        │    │ «PK» id          : VARCHAR(36)    │
│ name             : VARCHAR(200)   │◄───│ «PK,FK» user_id           │    │ name             : VARCHAR(255)   │
│ description      : TEXT           │ 1  │ role       : ENUM         │    │ description      : TEXT           │
│ color            : VARCHAR(7)     │    │ joined_at  : DATETIME     │    │ type             : ENUM           │
│ icon             : VARCHAR(50)    │ N  └───────────────────────────┘    │ mime_type        : VARCHAR(100)   │
│ status           : ENUM           │                                     │ file_size        : BIGINT         │
│ priority         : ENUM           │                                     │ file_path        : VARCHAR(500)   │
│ progress         : TINYINT        │                                     │ «FK» parent_id   : VARCHAR(36)    │◄──┐
│ start_date       : DATE           │                                     │ «FK» project_id  : VARCHAR(36)    │───┤
│ end_date         : DATE           │◄────────────────────────────────────│ is_starred       : TINYINT(1)     │   │
│ budget           : DECIMAL(15,2)  │ 1                                 N │ download_count   : INT            │   │
│ «FK» created_by  : VARCHAR(36)    │                                     │ «FK» uploaded_by : VARCHAR(36)    │   │
│ created_at       : DATETIME       │                                     │ created_at       : DATETIME       │   │
│ updated_at       : DATETIME       │                                     │ updated_at       : DATETIME       │   │
├───────────────────────────────────┤                                     ├───────────────────────────────────┤   │
│ «INDEX» idx_projects_status       │                                     │ «INDEX» idx_docs_parent           │───┘
│ «INDEX» idx_projects_priority     │                                     │ «INDEX» idx_docs_project          │
│ «INDEX» idx_projects_created_by   │                                     │ «INDEX» idx_docs_type             │
│ «INDEX» idx_projects_dates        │                                     │ «INDEX» idx_docs_starred          │
└───────────────────────────────────┘                                     └───────────────────────────────────┘
                                                                                          │
                                                                                          │ 1
PROJECT STATUS ENUM:                    PROJECT_MEMBER ROLE ENUM:                         │
┌───────────────────────┐               ┌───────────────────────┐                         │ N
│ • planning            │               │ • owner   - Chủ sở hữu│               ┌─────────┴─────────────────────────┐
│ • active              │               │ • manager - Quản lý   │               │       DOCUMENT_SHARES             │
│ • on_hold             │               │ • member  - Thành viên│               ├───────────────────────────────────┤
│ • completed           │               │ • viewer  - Người xem │               │ «PK,FK» document_id : VARCHAR(36) │
│ • cancelled           │               └───────────────────────┘               │ «PK,FK» user_id     : VARCHAR(36) │
└───────────────────────┘                                                       │ permission          : ENUM        │
                                        DOCUMENT TYPE ENUM:                     │ shared_at           : DATETIME    │
PRIORITY ENUM:                          ┌───────────────────────┐               │ «FK» shared_by      : VARCHAR(36) │
┌───────────────────────┐               │ • folder  - Thư mục   │               └───────────────────────────────────┘
│ • low     - Thấp      │               │ • file    - Tệp tin   │
│ • medium  - Trung bình│               └───────────────────────┘               PERMISSION ENUM:
│ • high    - Cao       │                                                       ┌───────────────────────┐
│ • urgent  - Khẩn cấp  │                                                       │ • view  - Chỉ xem     │
└───────────────────────┘                                                       │ • edit  - Chỉnh sửa   │
                                                                                └───────────────────────┘
```

### 2.3. Nhóm Quản lý Công việc (Task Management)

```
┌─────────────────────────────────────────────────────────────────────────────────────┐
│                              TASK MANAGEMENT MODULE                                  │
└─────────────────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────┐                    ┌─────────────────────────────────────┐
│            PROJECTS                 │                    │              USERS                  │
├─────────────────────────────────────┤                    ├─────────────────────────────────────┤
│ «PK» id          : VARCHAR(36)      │                    │ «PK» id          : VARCHAR(36)      │
│ ...                                 │                    │ ...                                 │
└──────────────────┬──────────────────┘                    └──────────────────┬──────────────────┘
                   │                                                          │
                   │ 1                                                        │
                   │                                                          │
                   │ N                                                        │
┌──────────────────┴──────────────────┐                                       │
│              TASKS                  │                                       │
├─────────────────────────────────────┤                                       │
│ «PK» id          : VARCHAR(36)      │                                       │
│ «FK» project_id  : VARCHAR(36)      │                                       │
│ title            : VARCHAR(500)     │                                       │
│ description      : TEXT             │                                       │
│ status           : ENUM             │                                       │
│ priority         : ENUM             │                                       │
│ position         : INT UNSIGNED     │                                       │
│ start_date       : DATE             │                                       │
│ due_date         : DATE             │                                       │
│ completed_at     : DATETIME         │                                       │
│ estimated_hours  : DECIMAL(6,2)     │                                       │
│ actual_hours     : DECIMAL(6,2)     │                                       │
│ «FK» created_by  : VARCHAR(36)      │───────────────────────────────────────┤
│ created_at       : DATETIME         │                                       │
│ updated_at       : DATETIME         │                                       │
├─────────────────────────────────────┤                                       │
│ «INDEX» idx_tasks_project           │                                       │
│ «INDEX» idx_tasks_status            │                                       │
│ «INDEX» idx_tasks_priority          │                                       │
│ «INDEX» idx_tasks_due_date          │                                       │
│ «INDEX» idx_tasks_position          │                                       │
└──────────────────┬──────────────────┘                                       │
                   │                                                          │
       ┌───────────┼───────────┬───────────────────────────┐                  │
       │           │           │                           │                  │
       │ 1         │ 1         │ 1                         │ 1                │
       │           │           │                           │                  │
       │ N         │ N         │ N                         │ N                │
┌──────┴──────┐ ┌──┴───────┐ ┌─┴─────────────────┐ ┌───────┴───────────────┐  │
│TASK_ASSIGNEES│ │TASK_LABELS│ │ TASK_CHECKLISTS  │ │      COMMENTS         │  │
├─────────────┤ ├──────────┤ ├───────────────────┤ ├───────────────────────┤  │
│«PK,FK»task_id│ │«PK,FK»   │ │ «PK» id           │ │ «PK» id               │  │
│«PK,FK»user_id│─┤ task_id  │ │ «FK» task_id      │ │ entity_type : ENUM    │  │
│«FK»assigned_by│ │«PK,FK»   │ │ title             │ │ entity_id             │  │
│ assigned_at  │ │ label_id │ │ is_completed      │ │ content     : TEXT    │  │
└─────────────┘ │ created_at│ │ position          │ │ «FK» parent_id        │◄─┼──┐
       │        └──────────┘ │ completed_at      │ │ «FK» created_by       │──┤  │
       │              │      │ «FK» completed_by │─┤ created_at            │  │  │
       │              │ N    │ created_at        │ │ updated_at            │  │  │
       │              │      └───────────────────┘ └───────────────────────┘  │  │
       │              │ 1                                                     │  │
       │        ┌─────┴─────────────────┐                                     │  │
       │        │        LABELS         │                                     │  │
       │        ├───────────────────────┤                                     │  │
       │        │ «PK» id               │                                     │  │
       │        │ name     : VARCHAR(50)│                                     │  │
       │        │ color    : VARCHAR(7) │                                     │  │
       │        │ description           │                                     │  │
       │        │ created_at            │                                     │  │
       │        └───────────────────────┘                                     │  │
       │                                                                      │  │
       └──────────────────────────────────────────────────────────────────────┘  │
                                                                                 │
                                                    (Self-referencing for ◄──────┘
                                                     nested replies)

TASK STATUS ENUM (Kanban Columns):
┌─────────────────────────────────────────────────────────────────────────────────────┐
│  BACKLOG  ──►  TODO  ──►  IN_PROGRESS  ──►  IN_REVIEW  ──►  DONE                   │
│  (Chờ xử lý)  (Cần làm)   (Đang làm)       (Đang review)   (Hoàn thành)            │
└─────────────────────────────────────────────────────────────────────────────────────┘

ENTITY_TYPE ENUM (for Comments):
┌───────────────────────┐
│ • task     - Công việc│
│ • document - Tài liệu │
│ • project  - Dự án    │
└───────────────────────┘
```


### 2.4. Nhóm Lịch và Sự kiện (Calendar & Events)

```
┌─────────────────────────────────────────────────────────────────────────────────────┐
│                            CALENDAR & EVENTS MODULE                                  │
└─────────────────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────┐    ┌─────────────────────────────────────┐
│            PROJECTS                 │    │              TASKS                  │
├─────────────────────────────────────┤    ├─────────────────────────────────────┤
│ «PK» id          : VARCHAR(36)      │    │ «PK» id          : VARCHAR(36)      │
│ ...                                 │    │ ...                                 │
└──────────────────┬──────────────────┘    └──────────────────┬──────────────────┘
                   │                                          │
                   │ 1                                        │ 1
                   │                                          │
                   │ N                                        │ N
                   │         ┌────────────────────────────────┘
                   │         │
                   │         │
┌──────────────────┴─────────┴────────┐                    ┌─────────────────────────────────────┐
│          CALENDAR_EVENTS            │                    │              USERS                  │
├─────────────────────────────────────┤                    ├─────────────────────────────────────┤
│ «PK» id          : VARCHAR(36)      │                    │ «PK» id          : VARCHAR(36)      │
│ title            : VARCHAR(255)     │                    │ ...                                 │
│ description      : TEXT             │                    └──────────────────┬──────────────────┘
│ type             : ENUM             │                                       │
│ color            : VARCHAR(7)       │                                       │
│ start_time       : DATETIME         │                                       │
│ end_time         : DATETIME         │                                       │
│ is_all_day       : TINYINT(1)       │                                       │
│ location         : VARCHAR(255)     │                                       │
│ «FK» project_id  : VARCHAR(36)      │                                       │
│ «FK» task_id     : VARCHAR(36)      │                                       │
│ «FK» created_by  : VARCHAR(36)      │───────────────────────────────────────┤
│ created_at       : DATETIME         │                                       │
│ updated_at       : DATETIME         │                                       │
├─────────────────────────────────────┤                                       │
│ «INDEX» idx_events_dates            │                                       │
│ «INDEX» idx_events_type             │                                       │
│ «INDEX» idx_events_project          │                                       │
│ «INDEX» idx_events_created_by       │                                       │
└──────────────────┬──────────────────┘                                       │
                   │                                                          │
                   │ 1                                                        │
                   │                                                          │
                   │ N                                                        │
┌──────────────────┴──────────────────┐                                       │
│          EVENT_ATTENDEES            │                                       │
├─────────────────────────────────────┤                                       │
│ «PK,FK» event_id : VARCHAR(36)      │                                       │
│ «PK,FK» user_id  : VARCHAR(36)      │───────────────────────────────────────┘
│ status           : ENUM             │
│ responded_at     : DATETIME         │
├─────────────────────────────────────┤
│ «INDEX» idx_ea_user                 │
│ «INDEX» idx_ea_status               │
└─────────────────────────────────────┘


EVENT TYPE ENUM:                        ATTENDEE STATUS ENUM:
┌───────────────────────┐               ┌───────────────────────┐
│ • meeting  - Họp      │               │ • pending   - Chờ     │
│ • deadline - Deadline │               │ • accepted  - Đồng ý  │
│ • reminder - Nhắc nhở │               │ • declined  - Từ chối │
│ • event    - Sự kiện  │               │ • tentative - Tạm thời│
└───────────────────────┘               └───────────────────────┘
```

### 2.5. Nhóm Ghi log Hoạt động (Activity Logging)

```
┌─────────────────────────────────────────────────────────────────────────────────────┐
│                            ACTIVITY LOGGING MODULE                                   │
└─────────────────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────┐
│              USERS                  │
├─────────────────────────────────────┤
│ «PK» id          : VARCHAR(36)      │
│ ...                                 │
└──────────────────┬──────────────────┘
                   │
                   │ 1
                   │
                   │ N
┌──────────────────┴──────────────────┐
│          ACTIVITY_LOGS              │
├─────────────────────────────────────┤
│ «PK» id          : VARCHAR(36)      │
│ «FK» user_id     : VARCHAR(36)      │
│ entity_type      : VARCHAR(50)      │  ◄── project, task, document, user, comment...
│ entity_id        : VARCHAR(36)      │
│ action           : VARCHAR(50)      │  ◄── created, updated, deleted, assigned...
│ old_values       : JSON             │  ◄── Giá trị trước khi thay đổi
│ new_values       : JSON             │  ◄── Giá trị sau khi thay đổi
│ ip_address       : VARCHAR(45)      │  ◄── IPv4 hoặc IPv6
│ user_agent       : VARCHAR(500)     │  ◄── Browser/Device info
│ created_at       : DATETIME         │
├─────────────────────────────────────┤
│ «INDEX» idx_activity_user           │
│ «INDEX» idx_activity_entity         │
│ «INDEX» idx_activity_action         │
│ «INDEX» idx_activity_created_at     │
└─────────────────────────────────────┘


Ví dụ dữ liệu Activity Log:
┌─────────────────────────────────────────────────────────────────────────────────────┐
│ {                                                                                   │
│   "id": "uuid-xxx",                                                                 │
│   "user_id": "uuid-user",                                                           │
│   "entity_type": "task",                                                            │
│   "entity_id": "uuid-task",                                                         │
│   "action": "updated",                                                              │
│   "old_values": {"status": "todo", "priority": "medium"},                           │
│   "new_values": {"status": "in_progress", "priority": "high"},                      │
│   "ip_address": "192.168.1.100",                                                    │
│   "user_agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64)...",                     │
│   "created_at": "2024-12-23 10:30:00"                                               │
│ }                                                                                   │
└─────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 3. BẢNG TỔNG HỢP CÁC THỰC THỂ

| STT | Tên bảng | Mô tả | Số cột | Khóa chính | Khóa ngoại |
|-----|----------|-------|--------|------------|------------|
| 1 | users | Người dùng | 16 | id | - |
| 2 | user_settings | Cài đặt người dùng | 11 | user_id | user_id → users |
| 3 | projects | Dự án | 14 | id | created_by → users |
| 4 | project_members | Thành viên dự án | 4 | (project_id, user_id) | project_id → projects, user_id → users |
| 5 | tasks | Công việc | 15 | id | project_id → projects, created_by → users |
| 6 | task_assignees | Người được giao task | 4 | (task_id, user_id) | task_id → tasks, user_id → users, assigned_by → users |
| 7 | task_checklists | Checklist của task | 8 | id | task_id → tasks, completed_by → users |
| 8 | task_labels | Nhãn của task | 3 | (task_id, label_id) | task_id → tasks, label_id → labels |
| 9 | labels | Nhãn | 5 | id | - |
| 10 | documents | Tài liệu | 14 | id | parent_id → documents, project_id → projects, uploaded_by → users |
| 11 | document_shares | Chia sẻ tài liệu | 5 | (document_id, user_id) | document_id → documents, user_id → users, shared_by → users |
| 12 | comments | Bình luận | 8 | id | parent_id → comments, created_by → users |
| 13 | notifications | Thông báo | 9 | id | user_id → users |
| 14 | calendar_events | Sự kiện lịch | 14 | id | project_id → projects, task_id → tasks, created_by → users |
| 15 | event_attendees | Người tham dự | 4 | (event_id, user_id) | event_id → calendar_events, user_id → users |
| 16 | activity_logs | Lịch sử hoạt động | 10 | id | user_id → users |

---

## 4. QUAN HỆ GIỮA CÁC THỰC THỂ

### 4.1. Quan hệ 1-1 (One-to-One)

| Thực thể 1 | Thực thể 2 | Mô tả |
|------------|------------|-------|
| users | user_settings | Mỗi user có 1 bản ghi settings |

### 4.2. Quan hệ 1-N (One-to-Many)

| Thực thể 1 (1) | Thực thể N (N) | Mô tả |
|----------------|----------------|-------|
| users | projects | User tạo nhiều projects |
| users | tasks | User tạo nhiều tasks |
| users | documents | User upload nhiều documents |
| users | comments | User tạo nhiều comments |
| users | notifications | User có nhiều notifications |
| users | activity_logs | User có nhiều activity logs |
| projects | tasks | Project có nhiều tasks |
| projects | documents | Project có nhiều documents |
| projects | calendar_events | Project có nhiều events |
| tasks | task_checklists | Task có nhiều checklist items |
| tasks | comments | Task có nhiều comments |
| documents | documents | Document có nhiều sub-documents (thư mục) |
| comments | comments | Comment có nhiều replies (nested) |
| calendar_events | event_attendees | Event có nhiều attendees |
| labels | task_labels | Label được gán cho nhiều tasks |

### 4.3. Quan hệ N-N (Many-to-Many)

| Thực thể 1 | Thực thể 2 | Bảng trung gian | Mô tả |
|------------|------------|-----------------|-------|
| projects | users | project_members | Nhiều users tham gia nhiều projects |
| tasks | users | task_assignees | Nhiều users được giao nhiều tasks |
| tasks | labels | task_labels | Nhiều tasks có nhiều labels |
| documents | users | document_shares | Nhiều documents được share cho nhiều users |
| calendar_events | users | event_attendees | Nhiều users tham dự nhiều events |

### 4.4. Quan hệ Self-Referencing

| Thực thể | Cột tham chiếu | Mô tả |
|----------|----------------|-------|
| documents | parent_id | Cấu trúc thư mục phân cấp |
| comments | parent_id | Bình luận lồng nhau (nested replies) |

---

## 5. RÀNG BUỘC TOÀN VẸN

### 5.1. Ràng buộc khóa chính (Primary Key)

- Tất cả các bảng sử dụng UUID (VARCHAR(36)) làm khóa chính
- Các bảng trung gian sử dụng khóa chính kết hợp

### 5.2. Ràng buộc khóa ngoại (Foreign Key)

```sql
-- Ví dụ ràng buộc khóa ngoại
CONSTRAINT `fk_tasks_project` FOREIGN KEY (`project_id`) 
    REFERENCES `projects`(`id`) ON DELETE CASCADE

CONSTRAINT `fk_tasks_created_by` FOREIGN KEY (`created_by`) 
    REFERENCES `users`(`id`) ON DELETE SET NULL

CONSTRAINT `fk_comments_parent` FOREIGN KEY (`parent_id`) 
    REFERENCES `comments`(`id`) ON DELETE CASCADE
```

### 5.3. Hành vi khi xóa (ON DELETE)

| Quan hệ | Hành vi | Giải thích |
|---------|---------|------------|
| projects → tasks | CASCADE | Xóa project sẽ xóa tất cả tasks |
| tasks → task_checklists | CASCADE | Xóa task sẽ xóa tất cả checklist items |
| tasks → comments | CASCADE | Xóa task sẽ xóa tất cả comments |
| comments → comments (replies) | CASCADE | Xóa comment cha sẽ xóa tất cả replies |
| documents → documents (children) | CASCADE | Xóa folder sẽ xóa tất cả files con |
| users → projects.created_by | SET NULL | Xóa user sẽ set created_by = NULL |
| users → tasks.created_by | SET NULL | Xóa user sẽ set created_by = NULL |

### 5.4. Ràng buộc UNIQUE

| Bảng | Cột | Mô tả |
|------|-----|-------|
| users | email | Email phải duy nhất |

### 5.5. Ràng buộc CHECK (ENUM)

| Bảng | Cột | Giá trị cho phép |
|------|-----|------------------|
| users | role | admin, manager, member, guest |
| projects | status | planning, active, on_hold, completed, cancelled |
| projects | priority | low, medium, high, urgent |
| tasks | status | backlog, todo, in_progress, in_review, done |
| tasks | priority | low, medium, high, urgent |
| documents | type | folder, file |
| comments | entity_type | task, document, project |
| calendar_events | type | meeting, deadline, reminder, event |
| event_attendees | status | pending, accepted, declined, tentative |
| document_shares | permission | view, edit |
| user_settings | theme | light, dark, system |

---

## 6. CHÚ THÍCH KÝ HIỆU

```
┌─────────────────────────────────────────────────────────────────────────────────────┐
│                              CHÚ THÍCH KÝ HIỆU ERD                                  │
└─────────────────────────────────────────────────────────────────────────────────────┘

Ký hiệu thực thể:
┌─────────────────────────────────────┐
│          TÊN_BẢNG                   │  ◄── Tên bảng (Entity)
├─────────────────────────────────────┤
│ «PK» id          : VARCHAR(36)      │  ◄── Khóa chính (Primary Key)
│ «FK» user_id     : VARCHAR(36)      │  ◄── Khóa ngoại (Foreign Key)
│ «UK» email       : VARCHAR(255)     │  ◄── Khóa duy nhất (Unique Key)
│ «PK,FK» project_id                  │  ◄── Vừa là PK vừa là FK
│ name             : VARCHAR(100)     │  ◄── Thuộc tính thường
├─────────────────────────────────────┤
│ «INDEX» idx_name                    │  ◄── Index
└─────────────────────────────────────┘

Ký hiệu quan hệ:
────────────────  Quan hệ 1-1 (One-to-One)
        │
        │ 1
        │
        │ 1
────────────────

────────────────  Quan hệ 1-N (One-to-Many)
        │
        │ 1
        │
        │ N
────────────────

◄───────────────  Hướng tham chiếu (FK → PK)

◄──┐              Self-referencing (tự tham chiếu)
   │
───┘
```

