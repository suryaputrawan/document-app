This application uses Laravel.

The default password is:
Username : superadmin
Password: password

Please set permissions in the assign permission menu first so that they can be used for all users who are assigned according to the assigned role.

Images are stored in storage, so when trying locally, run php storage:link first.
If it has been deployed on a server, remember to create a symlink in the storage/public folder.

There is an automatic check on the certificate which will expire 7 days from the entered expiration date. Create a cron job on the server to run commands automatically. The command name is certificates:enddate.
