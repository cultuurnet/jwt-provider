namespace 'jwt-provider' do
  desc "Build binaries"
  task :build do |task|
    system('composer2 install --no-dev --ignore-platform-reqs --prefer-dist --optimize-autoloader') or exit 1
  end
  desc "remove debug files"
  task :remove_debug_files do |task|
      system('rm web/jwt-example*.php')
  end
end
