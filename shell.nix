with import <nixpkgs> {};
pkgs.mkShell {
  buildInputs = with pkgs; [
    nodejs_20
    nodePackages.npm
    php83
    php83Packages.composer
  ];

  shellHook = ''
    alias sail='sh $([ -f sail ] && echo sail || echo vendor/bin/sail)'
  '';
}
