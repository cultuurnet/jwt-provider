namespace 'jwt-provider-uitidv1' do
  desc "Build binaries"
  task :build do |task|
    system('composer2 install --no-dev --ignore-platform-reqs --prefer-dist --optimize-autoloader') or exit 1
  end
end
