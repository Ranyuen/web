# coding=utf-8
require 'json'
require 'singleton'
require 'rexml/document'
require 'rexml/formatters/pretty'

module Nav
  class Generator
    # Prepare navs.
    # param [String] dir
    # return [Nav::Generator]
    def gather dir; @nav = gather_recur dir; self; end

    # return [String]
    def to_json; @nav.to_json; end

    # return [String] sitemap XML string.
    def sitemap
      sitemap = REXML::Document.new <<XML
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
</urlset>
XML
      gen_sitemap_part(@nav['en'], 'http://ranyuen.com/en/').
        each{|url| sitemap.root.add_element url }
      gen_sitemap_part(@nav['ja'], 'http://ranyuen.com/').
        each{|url| sitemap.root.add_element url }
      Nav.xml_to_s sitemap
    end

    private

    def gather_recur dir
      nav = {}
      cwd = File.absolute_path '.'
      Dir.chdir dir
      dirs, files = Dir.foreach('.').
        reject{|f| f =~ /^(?:\.|error)/ }.
        partition{|f| File.directory? f }
      files.select{|f| File.extname(f) == '.markdown' }.each do |f|
        meta = get_meta_of f
        f = Nav.file_basename f
        lang = (f.match(/\.([^.]+)$/) || [])[1] || 'ja'
        nav[lang] ||= {}
        nav[lang][Nav.file_basename f] = meta #[:title]
      end
      dirs.each do |f|
        gather_recur(File.absolute_path f).each do |lang, sub_nav|
          nav[lang] ||= {}
          nav[lang][f] = sub_nav
        end
      end
      Dir.chdir cwd
      nav
    end

    def get_meta_of filename
      content = open(filename, 'r:utf-8'){|f| f.read.chomp }
      return {} unless content =~ /^---/
      get_item = -> name, default = '' do
        v = (content.lines.find{|line| line =~ %r{^#{name}\s*:} } || "#{name}:").
          match(%r{^#{name}\s*:\s*(.*)$})[1].chomp
        v == '' ? default : v
      end
      title = get_item.call 'title'
      lastmod = get_item.call 'lastmod',
        File.mtime(filename).strftime('%Y-%m-%dT%H:%M:%SZ')
      {
        title:   title,
        lastmod: lastmod,
      }
    end

    def gen_sitemap_part nav, base_url
      nodes = []
      metas, sub_navs = nav.partition{|key, meta| meta[:title] }
      metas.each do |key, meta|
        url_node = REXML::Element.new 'url'
        loc_node = REXML::Element.new 'loc'
        loc_node.text = "#{base_url}#{key == 'index' ? '' : key}"
        url_node.add_element loc_node
        lastmod_node = REXML::Element.new 'lastmod'
        lastmod_node.text = meta[:lastmod]
        url_node.add_element lastmod_node
        changefreq_node = REXML::Element.new 'changefreq'
        changefreq_node.text = 'daily'
        url_node.add_element changefreq_node
        nodes << url_node
      end
      sub_navs.each do |key, sub_nav|
        nodes += gen_sitemap_part sub_nav, "#{base_url}#{key}/"
      end
      nodes
    end
  end

  class Navs
    include Singleton

    def initialize
      @generator = Generator.new
      @generator.gather File.absolute_path('templates')
    end

    def to_json; @generator.to_json; end

    def to_sitemap_xml; @generator.sitemap; end
  end

  # @param [String] filename
  # @return [String]
  def self.file_basename filename
    File.basename filename, File.extname(filename)
  end

  # @param [REXML::Document] xml
  # @return [String]
  def self.xml_to_s xml
    formatter = REXML::Formatters::Pretty.new
    formatter.compact = true
    formatter.width = 120
    io = StringIO.new
    formatter.write xml, io
    io.string
  end
end

namespace :nav do
  desc 'Generate site navigation JSON.'
  task :nav do
    nav = Nav::Navs.instance.to_json
    open('templates/nav.json', 'w:utf-8'){|f| f.write nav }
    puts 'Generate navigation at templates/nav.json'
  end

  desc 'Generate sitemap.xml'
  task :sitemap do
    sitemap = Nav::Navs.instance.to_sitemap_xml
    open('sitemap.xml', 'w:utf-8'){|f| f.write sitemap }
    puts 'Generate sitemap at sitemap.xml'
  end
end
