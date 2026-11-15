# Staff invitation registration

Admin staff invitations use the origin and base path of the current admin request when building the copyable registration URL. This preserves the actual host, port, scheme, and any WAMP project subdirectory. Open the admin site using the same address staff will use. Set `APP_URL` to the canonical application origin for CLI-generated links and other URL generation outside a web request; include HTTPS in production.

No mail transport is required. Admin generates the link from **Admin → Staff → Send Invitation** (or **Regenerate Invitation**) and copies it from the generated invitation panel. Share it through any channel. Links expire after 24 hours; regenerating replaces the stored hash so the earlier link stops working.

Only records with `staff.role` set to `staff` are eligible. The current schema has no separate staff activation field or `users.staff_id` foreign key, so account association and role authorization continue to use the existing staff email match. New invited accounts are email-verified at creation so they can use the existing verified staff dashboard routes.
