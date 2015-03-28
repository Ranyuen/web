#!/usr/bin/env ruby
# coding=utf-8

require 'json'
require 'net/http'
require 'uri'

class Article
  attr_accessor :path, :ja_content, :en_content

  def initialize path, ja_content, en_content
    @path       = path
    @ja_content = ja_content
    @en_content = en_content
  end

  def valid?
    raise "Path is nil: #{to_json}"          unless @path && @path.start_with?('/')
    raise "All contents are nil: #{to_json}" unless @ja_content || @en_content
    true
  end

  def to_json
    hash = {
      id:       0,
      path:     @path,
      contents: [],
    }
    if @ja_content
      hash[:contents] << {
        lang:    'ja',
        content: @ja_content,
      }
    end
    if @en_content
      hash[:contents] << {
        lang:    'en',
        content: @en_content,
      }
    end
    hash.to_json
  end
end

class AdminClient
  def initialize host
    @host      = host
    @phpsessid = nil
  end

  def login username, password
    res = Net::HTTP.post_form URI.parse("#@host/admin/login"),
      username: username,
      password: password
    raise res.to_s unless res.is_a? Net::HTTPRedirection
    res.get_fields('Set-Cookie').each do |field|
      key, value = field.split(';')[0].split '='
      if 'PHPSESSID' == key
        @phpsessid = value
        break
      end
    end
  end

  def create_article article
    article.valid?
    uri = URI.parse "#@host/admin/articles/update/0"
    req = Net::HTTP::Put.new uri.path
    req['Cookie'] = "PHPSESSID=#@phpsessid"
    req.set_form_data article: article.to_json
    res = Net::HTTP.new(uri.host, uri.port).start{|http| http.request req }
    raise res.to_s unless res.is_a? Net::HTTPSuccess
    created_article = JSON.parse res.body
    puts "Created #{created_article['id']}: #{created_article['path']}"
  end
end

def each_article &block
  Dir['**/**/*.markdown'].
    map{|filename| /\A(.+)\.\w+\.markdown\z/.match(filename)[1] }.
    uniq.
    each do |path|
      filename = "#{path}.ja.markdown"
      ja_content = open filename, 'r:utf-8', &:read if File.exist? filename
      filename = "#{path}.en.markdown"
      en_content = open filename, 'r:utf-8', &:read if File.exist? filename
      path = /\A(.*)index\z/.match(path)[1] if %r{(?:\A|/)index\z} =~ path
      block.call Article.new("/#{path}", ja_content, en_content)
    end
end

def each_news &block
  Dir['news/*.md'].
    sort.
    each do |filename|
      path = %r{\Anews/\d+ (.+)\.ja\.md\z}.match(filename)[1]
      ja_content = open filename, 'r:utf-8', &:read
      block.call Article.new("/news/#{path}", ja_content, nil)
    end
end

def each_columns &block
  Dir['columns/*.md'].
    sort.
    each do |filename|
      path = %r{\Acolumns/\d+ (.+)\.ja\.md\z}.match(filename)[1]
      ja_content = open filename, 'r:utf-8', &:read
      block.call Article.new("/columns/#{path}", ja_content, nil)
    end
end


HOST     = 'http://localhost:3000'
USERNAME = 'ne_sachirou'
PASSWORD = 'password'
client = AdminClient.new HOST
client.login USERNAME, PASSWORD
each_article{|article| client.create_article article }
each_news{|article| client.create_article article }
each_columns{|article| client.create_article article }
