
	git clone https://github.com/ibidem/mjolnir-template-app.git ~/demo/0.1.x
	cd ~/demo/0.1.x
	git checkout mj/2.x/blank
	git remote rename origin mjolnir
	git remote add origin YOUR_PROJECT_URL.git
	git checkout -b development

We generally recommend the following branch structure:

 * `production` - self explanatory, whatever is in production is "always
   ready to be pulled in a live version," so avoid direct work on it
   outside of merges
 * `development` - integration branch for unstable features
 * `fixes` - very minor changes that don't require special "feature
   branches" or too much testing; for example: style fixes, typos,
   single-line fixes, formatting, very minor bugs, etc
 * misc feature branches for anything significant; when branches are merged
   into development remove them

In `fixes` you should only pull changes from production. All feature branches
should merge into development for integration (never into production directly).
Everything can pull from `fixes` and `production`. Development is merged into
production whenever it's current state has been tested.

In the above when cloning `YOUR_PROJECT_URL.git` we recommend using the ssh
version of url.

The reason we are keeping the template is to be able to pull tweaks and changes
to it in time, eg. changes to project `drafts/` (will be discussed later).