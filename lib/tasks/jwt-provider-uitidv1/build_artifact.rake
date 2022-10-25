require 'git'

namespace 'jwt-provider-uitidv1' do
  desc "Create a debian package from the binaries."
  task :build_artifact do |task|
  
    calver_version = ENV['PIPELINE_VERSION'].nil? ? Time.now.strftime("%Y.%m.%d.%H%M%S") : ENV['PIPELINE_VERSION']
    git_short_ref  = `git rev-parse --short HEAD`.strip
    version        = ENV['ARTIFACT_VERSION'].nil? ? "#{calver_version}+sha.#{git_short_ref}" : ENV['ARTIFACT_VERSION']
    artifact_name  = 'uitdatabank-jwt-provider-uitidv1'
    vendor         = 'publiq VZW'
    maintainer     = 'Infra publiq <infra@publiq.be>'
    license        = 'Apache-2.0'
    description    = 'JSON Web Token provider for UiTDatabank 3'
    source         = 'https://github.com/cultuurnet/jwt-provider'

    # git parameters
    git_url             = 'git@github.com:cultuurnet/jwt-provider'
    git_tag             = 'refs/tags/uitidv1'
    git_checkout_folder = ENV['GIT_CHECKOUT_FOLDER'].nil? ? "jwt-provider-uitidv1" : ENV['GIT_CHECKOUT_FOLDER']

    # clone the jwt-provider repo to a separate folder and checkout the uitidv1 git tag
    g = Git.clone(git_url, git_checkout_folder)
    g.checkout(git_tag)

    # change into the cloned jwt-provider folder
    # build the software and the package
    g.chdir do
      system('composer2 install --no-dev --ignore-platform-reqs --prefer-dist --optimize-autoloader') or exit 1

      FileUtils.mkdir_p('pkg')
      FileUtils.touch('config.yml')
  
      system("fpm -s dir -t deb -n #{artifact_name} -v #{version} -a all -p pkg \
        -x '.git*' -x pkg -x config.dist.yml -x 'Gemfile*' -x Jenkinsfile -x .bundle -x vendor/bundle \
        --config-files /var/www/udb3-jwt-provider-uitidv1/config.yml \
        --prefix /var/www/udb3-jwt-provider-uitidv1 \
        --deb-user www-data --deb-group www-data \
        --description '#{description}' --url '#{source}' --vendor '#{vendor}' \
        --license '#{license}' -m '#{maintainer}' \
        --deb-field 'Pipeline-Version: #{calver_version}' \
        --deb-field 'Git-Ref: #{git_short_ref}' \
        ."
      ) or exit 1
    end
  end
end
