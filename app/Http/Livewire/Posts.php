<?php

namespace App\Http\Livewire;

use App\Helper\MySlugHelper;
use App\Models\Post;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Posts extends Component
{

    use WithPagination, WithFileUploads;

    public $title;
    public $slug_url;
    public $body;
    public $image;
    public $post_image;
    public $post_image_name;
    public $modalId;
    public $modalFormVisible = false;
    public $confirmPostDeletion = false;


    public function showCreateModal() {
        $this->emit('createNewPostEmit');
        $this->modalFormReset();
        $this->modalFormVisible = true;
    }

    public function showUpdateModal($id)
    {
        $this->emit('updatePostEmit');
        $this->modalFormReset();
        $this->modalFormVisible = true;
        $this->modalId = $id;
        $this->loadModalData();
    }

    public function showDeleteModal($id)
    {
        $this->confirmPostDeletion = true;
        $this->modalId = $id;
    }

    public function modelData()
    {
        $data = [
            'title' => $this->title,
            'body' => $this->body,
        ];
        if ($this->post_image != ''){
            $data['image'] = $this->post_image_name;
        }

        return $data;
    }

    public function loadModalData()
    {
        $data = Post::find($this->modalId);
        $this->title = $data->title;
        $this->slug_url = $data->slug;
        $this->body = $data->body;
        $this->image = $data->image;
    }

    public function rules()
    {
        return [
            'title' => ['required'],
            'slug_url' => ['required', Rule::unique('posts', 'slug')->ignore($this->modalId)],
            'body' => ['required'],
            'post_image' => [Rule::requiredIf(!$this->modalId), 'max:1024']
        ];
    }

    public function modalFormReset()
    {
        $this->title = null;
        $this->slug_url = null;
        $this->body = null;
        $this->image = null;
        $this->post_image = null;
        $this->post_image_name = null;
        $this->modalId = null;
    }

    public function updatedTitle($value)
    {
        $this->slug_url = MySlugHelper::slug($value);
    }

    public function store()
    {

        $this->validate();
        if ($this->post_image != '') {
            $this->post_image_name = md5($this->post_image . microtime()).'.'.$this->post_image->extension();
            $this->post_image->storeAs('/', $this->post_image_name, 'uploads');
        }

        auth()->user()->posts()->create($this->modelData());

        $this->modalFormReset();
        $this->modalFormVisible = false;

        $this->alert('success', 'Post added successful!', [
            'position'  =>  'center',
            'timer'  =>  3000,
            'toast'  =>  true,
            'text'  =>  null,
            'showCancelButton'  =>  false,
            'showConfirmButton'  =>  false
        ]);

    }

    public function update()
    {
        $this->validate();
        $post = Post::where('id', $this->modalId)->first();
        if ($this->post_image != '') {
            if ($post->image != '') {
                if (File::exists('images/' . $post->image)) {
                    unlink('images/' . $post->image);
                }
            }
            $this->post_image_name = md5($this->post_image . microtime()).'.'.$this->post_image->extension();
            $this->post_image->storeAs('/', $this->post_image_name, 'uploads');
        }

        $post->update($this->modelData());

        $this->modalFormVisible = false;
        $this->modalFormReset();

        $this->alert('success', 'Post updated successful!', [
            'position'  =>  'center',
            'timer'  =>  3000,
            'toast'  =>  true,
            'text'  =>  null,
            'showCancelButton'  =>  false,
            'showConfirmButton'  =>  false
        ]);

    }

    public function destroy()
    {
        $post = Post::where('id', $this->modalId)->first();
        if ($post->image != '') {
            if (File::exists('images/' . $post->image)) {
                unlink('images/' . $post->image);
            }
        }

        $post->delete();
        $this->confirmPostDeletion = false;
        $this->resetPage();
        $this->alert('success', 'Post deleted successful!', [
            'position'  =>  'center',
            'timer'  =>  3000,
            'toast'  =>  true,
            'text'  =>  null,
            'showCancelButton'  =>  false,
            'showConfirmButton'  =>  false
        ]);

    }

    public function all_posts()
    {
        return Post::orderByDesc('id')->paginate(5);
    }

    public function render()
    {
        return view('livewire.posts', [
            'posts' => $this->all_posts()
        ]);
    }
}
