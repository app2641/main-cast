set :application, "STAR"
set :repository,  "git@github.com:app2641/{:apps}.git"

set :scm, :git
set :deploy_to, "/usr/share/nginx/{:apps}"
set :git_shallow_clone, 1
set :keep_release, 3
set :deploy_via, :remote_cache
set :branch, "master"

set :user, "app2641"
set :use_sudo, true
ssh_options[:auth_methods] = %w( publickey )
ssh_options[:keys] = %w( ~/.ssh/sakuravps )
ssh_options[:forward_agent] = %w( true )
ssh_options[:port] = "10022"
default_run_options[:pty]=true

role :web, "49.212.175.51" 


# set :scm, :git # You can set :scm explicitly or Capistrano will make an intelligent guess based on known version control directory names
# Or: `accurev`, `bzr`, `cvs`, `darcs`, `git`, `mercurial`, `perforce`, `subversion` or `none`

#role :web, "your web-server here"                          # Your HTTP server, Apache/etc
#role :app, "your app-server here"                          # This may be the same as your `Web` server
#role :db,  "your primary db-server here", :primary => true # This is where Rails migrations will run
#role :db,  "your slave db-server here"

# if you want to clean up old releases on each deploy uncomment this:
# after "deploy:restart", "deploy:cleanup"

# if you're still using the script/reaper helper you will need
# these http://github.com/rails/irs_process_scripts

# If you are using Passenger mod_rails uncomment this:
# namespace :deploy do
#   task :start do ; end
#   task :stop do ; end
#   task :restart, :roles => :app, :except => { :no_release => true } do
#     run "#{try_sudo} touch #{File.join(current_path,'tmp','restart.txt')}"
#   end
# end
