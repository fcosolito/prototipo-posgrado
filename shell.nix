let
  pkgs = import <nixpkgs> { };

  packages =
    let
    # Includes php-fpm by default
      phpForRuntime = (pkgs.php83.buildEnv {
        extensions = ({ enabled, all }: enabled ++ (with all; [
          intl
          pdo_pgsql
          xsl
          amqp
          gd
          openssl
          sodium
        ]));
        # extraConfig = ''
          # xdebug.mode=debug
        #'';
      });
      phpForComposer = (pkgs.php83.buildEnv {
        extensions = ({ enabled, all }: enabled ++ (with all; [
          xsl
        ]));
      });
    in
    [
      phpForRuntime
      phpForComposer.packages.composer
      pkgs.symfony-cli
      pkgs.nodejs
    ];
in
pkgs.mkShell {
  inherit packages;

  shellHook = ''
    export PATH=$PATH:/home/franco/Projects/learning-php/composer
  '';
}
