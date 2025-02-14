desc 'Create a debian package from the binaries.'
task :build_artifact do |task|

  calver_version = ENV['PIPELINE_VERSION'].nil? ? Time.now.strftime("%Y.%m.%d.%H%M%S") : ENV['PIPELINE_VERSION']
  git_short_ref  = `git rev-parse --short HEAD`.strip
  version        = ENV['ARTIFACT_VERSION'].nil? ? "#{calver_version}+sha.#{git_short_ref}" : ENV['ARTIFACT_VERSION']
  artifact_name  = 'uitdatabank-jwt-provider'
  vendor         = 'publiq VZW'
  maintainer     = 'Infra publiq <infra@publiq.be>'
  license        = 'Apache-2.0'
  description    = 'JSON Web Token provider for UiTdatabank'
  source         = 'https://github.com/cultuurnet/jwt-provider'
  build_url      = ENV['JOB_DISPLAY_URL'].nil? ? '' : ENV['JOB_DISPLAY_URL']

  basedir        = '/var/www/jwt-provider'

  FileUtils.mkdir_p('pkg')
  FileUtils.touch('config.yml')

  system("fpm -s dir -t deb -n #{artifact_name} -v #{version} -a all -p pkg \
    -x '.git*' -x pkg -x 'Gemfile*' -x Jenkinsfile -x .bundle -x vendor/bundle \
    --prefix #{basedir} --config-files #{basedir}/config.yml \
    --deb-user www-data --deb-group www-data \
    --description '#{description}' --url '#{source}' --vendor '#{vendor}' \
    --license '#{license}' -m '#{maintainer}' \
    --deb-field 'Pipeline-Version: #{calver_version}' \
    --deb-field 'Build-Url: #{build_url}' \
    --deb-field 'Git-Ref: #{git_short_ref}' \
    ."
  ) or exit 1

end
