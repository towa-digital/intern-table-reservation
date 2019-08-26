# Permission system

The permission system is defined in permissions.php. This file is included by require_once every time a page in the admin panel is openend. 

There are three different user roles in this plugin:

## Employee
- Slug: employee
- Display name: Angestellter
- Permissions:
    - tv_viewReservations
    - tv_viewTables
    - tv_addReservations

## Super Employee
- Slug: superEmployee
- Display name: Super-Angestellter
- Permissions:
    - **all Permissions of employee**
    - tv_exportReservations
    - tv_editReservations
    - tv_deleteReservations

## Owner
- Slug: owner
- Display name: Betreiber
- Permissions:
    - **all Permissions of superEmployee**
    - tv_editOptions
    - tv_addTables
    - tv_editTables
    - tv_deleteTables